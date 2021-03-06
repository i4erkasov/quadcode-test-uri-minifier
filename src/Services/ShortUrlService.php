<?php

namespace App\Services;

use App\Entity\ShortUrl;
use App\Exceptions\AppInvalidParametersException;
use App\Repository\ShortUrlsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\LockInterface;

class ShortUrlService
{
    private int $min;

    private int $max;

    private StringGeneratorService $generator;

    private EntityManagerInterface $em;

    private LockInterface $lock;

    private const CACHE_KEY_PREFIX = 'SHORTENING_LIMIT_';

    /**
     * ShortUrlService constructor.
     *
     * @param array                  $config
     * @param StringGeneratorService $generator
     * @param LockFactory            $lockFactory
     * @param EntityManagerInterface $em
     */
    public function __construct(
        array $config,
        StringGeneratorService $generator,
        LockFactory $lockFactory,
        EntityManagerInterface $em
    )
    {
        $this->generator = $generator;
        $this->lock      = $lockFactory->createLock('CREATE_SHORT_URL');
        $this->em        = $em;

        $this->min = (int)$config['min_length'];
        $this->max = (int)$config['max_length'];
    }

    public static function makeShortUrl(string $schema, string $host, string $code)
    {
        return sprintf('%s://%s/%s', $schema, $host, $code);
    }

    /**
     * @param string $schema
     * @param string $host
     * @param string $url
     *
     * @throws \Throwable
     * @return array
     */
    public function createShortUrl(string $schema, string $host, string $url): array
    {
        $this->lock->acquire();

        $currentLength = apcu_fetch('CURRENT_LENGTH') ? apcu_fetch('CURRENT_LENGTH') : $this->min;

        while ($currentLength <= $this->max) {
            if ($this->getShorteningLimit($currentLength)) {
                break;
            }

            $currentLength = apcu_inc('CURRENT_LENGTH');
        }

        if ($this->max < $currentLength) {
            throw new AppInvalidParametersException("Привышен лимит вариантов");
        }

        /** @var ShortUrlsRepository $repository */
        $repository = $this->em->getRepository(ShortUrl::class);

        $limit = $this->getShorteningLimit($currentLength);
        $full  = $this->generator->getLimits($currentLength);

        $digit = $full - $limit;

        $code = $this->generator->generateCode($digit, $currentLength);

        $shortUrl = $repository->insert($url, $code);

        if (!$shortUrl) {
            throw new \RuntimeException("Ссылка не была создана. Код для вставки code : {$code}");
        }

        apcu_dec(self::CACHE_KEY_PREFIX . $currentLength);

        $shortUrl['short_url'] = static::makeShortUrl($schema, $host, $shortUrl['code']);

        $this->lock->refresh();

        return $shortUrl;
    }

    /**
     * @param int $length
     *
     * @throws \Doctrine\DBAL\DBALException
     * @return int
     */
    public function getShorteningLimit(int $length): int
    {
        if (!($length >= $this->min && $length <= $this->max)) {
            throw new AppInvalidParametersException("Допустимый диапазаон от {$this->min} до {$this->max}");
        }

        $cacheKey = self::CACHE_KEY_PREFIX . $length;

        if (apcu_exists($cacheKey)) {
            return apcu_fetch($cacheKey);
        }

        /** @var ShortUrlsRepository $repository */
        $repository = $this->em->getRepository(ShortUrl::class);

        $created = $repository->getCountByLengthCode($length);
        $full    = $this->generator->getLimits($length);

        $limit = $full - $created;

        apcu_add($cacheKey, $limit);

        return $limit;
    }

    /**
     * @param string $code
     *
     * @return ShortUrl|null
     */
    public function getShortUrlByCode(string $code): ?ShortUrl
    {
        /** @var ShortUrlsRepository $repository */
        $repository = $this->em->getRepository(ShortUrl::class);

        return $repository->findOneBy([
            'code' => $code,
        ]);
    }
}