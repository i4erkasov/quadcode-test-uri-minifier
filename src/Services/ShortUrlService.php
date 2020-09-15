<?php

namespace App\Services;

use App\Entity\Urls;
use App\Repository\UrlsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Exception\InvalidArgumentException;

class ShortUrlService
{
    private int $min;

    private int $max;

    private StringGeneratorService $generator;

    private EntityManagerInterface $em;

    private const CACHE_KEY_PREFIX = 'SHORTENING_LIMIT_';

    public function __construct(array $config, StringGeneratorService $generator, EntityManagerInterface $em)
    {
        $this->generator = $generator;
        $this->em        = $em;

        $this->min = (int)$config['min_length'];
        $this->max = (int)$config['max_length'];
    }

    /**
     * @param string $schema
     * @param string $host
     * @param string $url
     *
     * @throws \Throwable
     * @return string|null
     */
    public function createShortUrl(string $schema, string $host, string $url): ?string
    {
        $currentLength = apcu_fetch('CURRENT_LENGTH') ?? $this->min;

        while ($currentLength <= $this->max) {
            if ($this->getShorteningLimit($currentLength)) {
                break;
            }

            $currentLength = apcu_inc('CURRENT_LENGTH');
        }

        if ($this->max < $currentLength) {
            die('Привышен лимит вариантов');
        }

        /** @var UrlsRepository $repository */
        $repository = $this->em->getRepository(Urls::class);

        $code = $this->generator->generateString($currentLength);

        while ($repository->isExistCode($code)) {
            //Генерируем short code пока не получим уникальный.
            $code = $this->generator->generateString($currentLength);
        }

        $repository->insert($url, $code);

        apcu_dec(self::CACHE_KEY_PREFIX . $currentLength);

        return sprintf('%s://%s/%s', $schema, $host, $code);
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
            throw new InvalidArgumentException(sprintf('Допустимы диапазаон от %s до %s', $this->min, $this->max));
        }

        $cacheKey = self::CACHE_KEY_PREFIX . $length;

        if (apcu_exists($cacheKey)) {
            return apcu_fetch($cacheKey);
        }

        /** @var UrlsRepository $repository */
        $repository = $this->em->getRepository(Urls::class);

        $created = $repository->getCountByLengthCode($length);
        $full    = $this->generator->getCountOptions($length);

        $limit = $full - $created;

        apcu_add($cacheKey, $limit);

        return $limit;
    }
}