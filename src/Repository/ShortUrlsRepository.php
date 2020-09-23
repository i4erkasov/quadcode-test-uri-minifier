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
     * @param string $url
     * @param string $code
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     * @throws \Throwable
     * @return array
     */
    public function insert(string $url, string $code): array
    {
        $em = $this->getEntityManager();

        $em->getConnection()->beginTransaction();

        try {
            $sql = 'SELECT id, url, code FROM short_urls WHERE url = :url AND removed = false';

            $statement = $em->getConnection()->executeQuery($sql, [
                'url' => $url,
            ]);

            $shortUrl = $statement->fetch();

            if ($shortUrl) {
                return $shortUrl;
            }

            $sql = 'INSERT INTO short_urls AS su (url, code, created_at)
            VALUES (:url, :code, :date_create) ON CONFLICT (code) DO
                UPDATE SET url = :url, removed = false
            WHERE su.id = (SELECT id FROM short_urls WHERE removed = true LIMIT 1)
            RETURNING su.id as id, su.url as url, su.code as code';

            $statement = $em->getConnection()->executeQuery($sql, [
                'url'         => $url,
                'code'        => $code,
                'date_create' => (new \DateTime('NOW'))->format('Y-m-d h:i:s'),
            ]);

            $result = $statement->fetch();

            $em->getConnection()->commit();

            return $result ? $result : [];

        } catch (\Throwable $ex) {
            $em->getConnection()->rollBack();

            throw $ex;
        }
    }
}
