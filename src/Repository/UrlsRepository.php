<?php

namespace App\Repository;

use App\Entity\Urls;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\ConnectionException;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Urls|null find($id, $lockMode = null, $lockVersion = null)
 * @method Urls|null findOneBy(array $criteria, array $orderBy = null)
 * @method Urls[]    findAll()
 * @method Urls[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UrlsRepository extends ServiceEntityRepository
{
    /**
     * UrlsRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Urls::class);
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
            'SELECT count(*) as count FROM urls AS u WHERE LENGTH(u.code) = :length'
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
            'SELECT EXISTS(SELECT 1 FROM urls WHERE code = :code)'
        );

        $statement->execute([
            'code' => $code,
        ]);

        $result = $statement->fetchAll();

        return (bool)$result;
    }

    public function insert(string $url, string $code): int
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
                return $shortUrl->getId();
            }

            $shortUrl = new Urls();

            $shortUrl->setUrl($url);
            $shortUrl->setCode($code);
            $shortUrl->setCreatedAt(new \DateTimeImmutable('NOW'));

            $em->persist($shortUrl);
            $em->flush();
            $em->getConnection()->commit();

            return $shortUrl->getId();
        } catch (\Throwable $ex) {
            $em->getConnection()->rollBack();

            throw $ex;
        }
    }

    public function updateById(int $id)
    {

    }

    // /**
    //  * @return Links[] Returns an array of Links objects
    //  */

    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->setLockMode(LockMode::PESSIMISTIC_READ)
            ->getResult();
    }


    /*
    public function findOneBySomeField($value): ?Links
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
