<?php

namespace App\Services;

use App\Entity\StatisticRedirect;
use App\Repository\StatisticRedirectRepository;
use Doctrine\ORM\EntityManagerInterface;

class StatisticRedirectService
{
    private EntityManagerInterface $em;

    /**
     * StatisticRedirectService constructor.
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;

    }

    /**
     * @param int $id
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Throwable
     */
    public function registrationRedirect(int $id): void
    {
        try {
            $this->em->getConnection()->beginTransaction();

            $staticRedirect = new StatisticRedirect();

            $staticRedirect->setUrlId($id);
            $staticRedirect->setRedirectDatetime(new \DateTimeImmutable('NOW'));

            $this->em->persist($staticRedirect);
            $this->em->flush();
            $this->em->getConnection()->commit();

        } catch (\Throwable $ex) {
            $this->em->getConnection()->rollBack();

            throw $ex;
        }
    }

    /**
     * @param int $page
     * @param int $limit
     *
     * @return array
     */
    public function getStatisticAll(int $page, int $limit): array
    {
        $offset = $limit * ($page - 1);

        /** @var StatisticRedirectRepository $repository */
        $repository = $this->em->getRepository(StatisticRedirect::class);

        return $repository->getStatisticAll($limit, $offset);
    }

    /**
     * @param int $id
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @return array|null
     */
    public function getStatisticById(int $id): ?array
    {
        /** @var StatisticRedirectRepository $repository */
        $repository = $this->em->getRepository(StatisticRedirect::class);

        $statisticRedirectOne = $repository->getStatisticById($id);

        if (!$statisticRedirectOne) {
            return null;
        }

        return $statisticRedirectOne;
    }
}