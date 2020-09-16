<?php

namespace App\Repository;

use App\Entity\ShortUrl;
use App\Entity\StatisticRedirect;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StatisticRedirect|null find($id, $lockMode = null, $lockVersion = null)
 * @method StatisticRedirect|null findOneBy(array $criteria, array $orderBy = null)
 * @method StatisticRedirect[]    findAll()
 * @method StatisticRedirect[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StatisticRedirectRepository extends ServiceEntityRepository
{
    /**
     * StatisticRedirectRepository constructor.
     *
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StatisticRedirect::class);
    }

    /**
     * @param int $limit
     * @param int $offset
     *
     * @return array|int|string
     */
    public function getStatisticAll(int $limit = 10, int $offset = 1)
    {
        return $this->createQueryBuilder('s')
            ->select('u.id as id, u.code as code, count(s.id) as redirect, u.url as url')
            ->leftJoin(ShortUrl::class, 'u', Join::WITH, 'u.id = s.urlId')
            ->orderBy('u.id', 'ASC')
            ->groupBy('u.id')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->getQuery()
            ->getArrayResult();
    }

    /**
     * @param int $id
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @return array|null
     */
    public function getStatisticById(int $id): ?array
    {
        return $this->createQueryBuilder('s')
            ->select('u.id as id, u.code as code, count(s.id) as redirect, u.url as url')
            ->leftJoin(ShortUrl::class, 'u', Join::WITH, 'u.id = s.urlId')
            ->andWhere('u.id = :id')
            ->setParameter('id', $id)
            ->groupBy('u.id')
            ->getQuery()
            ->getOneOrNullResult();
    }
}
