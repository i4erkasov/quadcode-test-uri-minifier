<?php

namespace App\Repository;

use App\Entity\ShortUrl;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\LockMode;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ShortUrl|null find($id, $lockMode = null, $lockVersion = null)
 * @method ShortUrl|null findOneBy(array $criteria, array $orderBy = null)
 * @method ShortUrl[]    findAll()
 * @method ShortUrl[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ShortUrlsRepository extends ServiceEntityRepository
{
    /**
     * ShortUrlsRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ShortUrl::class);
    }

    /**
     * @param int $length
     *
     * @throws \Doctrine\DBAL\DBALException
     * @return int
     */
    public function getCountByLengthCode(int $length): int
    {
        $connection = $this->getEntityManager()->getConnection();
        $statement  = $connection->prepare(
            'SELECT count(*) as count FROM short_urls AS u WHERE LENGTH(u.code) = :length'
        );

        $statement->execute([
            'length' => $length,
        ]);

        $result = $statement->fetchAll();

        return (int)($result['count'] ?? 0);
    }

    /**
     * @param $code
     *
     * @throws \Doctrine\DBAL\DBALException
     * @return bool
     */
    public function isExistCode($code): bool
    {
        $connection = $this->getEntityManager()->getConnection();
        $statement  = $connection->prepare(
            'SELECT EXISTS(SELECT 1 FROM short_urls WHERE code = :code)'
        );

        $statement->execute([
            'code' => $code,
        ]);

        $result = $statement->fetch();

        return (bool)$result['exists'];
    }

    /**
     * @param string $url
     * @param string $code
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     * @throws \Throwable
     * @return ShortUrl
     */
    public function insert(string $url, string $code): ShortUrl
    {
        $em = $this->getEntityManager();

        $em->getConnection()->beginTransaction();

        try {
            $shortUrl = $this->createQueryBuilder('u')
                ->andWhere('u.url = :url')
                ->setParameter('url', $url)
                ->getQuery()
                ->setLockMode(LockMode::PESSIMISTIC_READ)
                ->getOneOrNullResult();

            if ($shortUrl) {
                return $shortUrl;
            }

            $shortUrl = new ShortUrl();

            $shortUrl->setUrl($url);
            $shortUrl->setCode($code);
            $shortUrl->setCreatedAt(new \DateTimeImmutable('NOW'));

            $em->persist($shortUrl);
            $em->flush();
            $em->getConnection()->commit();

            return $shortUrl;
        } catch (\Throwable $ex) {
            $em->getConnection()->rollBack();

            throw $ex;
        }
    }
}
