<?php

namespace App\Controller;

use App\Requests\StatisticRedirectRequest;
use App\Services\ShortUrlService;
use App\Services\StatisticRedirectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StatisticRedirectController extends AbstractController
{
    /**
     * @param StatisticRedirectRequest $request
     * @param StatisticRedirectService $statisticService
     *
     * @return array
     */
    public function index(StatisticRedirectRequest $request, StatisticRedirectService $statisticService)
    {
        $page   = $request->getPage();
        $limit  = $request->getLimit();
        $schema = $request->getScheme();
        $host   = $request->getHost();

        $statistic = $statisticService->getStatisticAll($page, $limit);

        $result = array_map(function ($items) use ($schema, $host) {
            $items['short_url'] = ShortUrlService::makeShortUrl($schema, $host, $items['code']);

            unset($items['code']);

            return $items;
        }, $statistic);

        return [
            'items' => $result,
        ];
    }

    /**
     * @param int                      $id
     * @param StatisticRedirectRequest $request
     * @param StatisticRedirectService $statisticService
     *
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @return array|null
     */
    public function getById(int $id, StatisticRedirectRequest $request, StatisticRedirectService $statisticService)
    {
        $schema = $request->getScheme();
        $host   = $request->getHost();

        $statistic = $statisticService->getStatisticById($id);

        if ($statistic) {
            $statistic['short_url'] = ShortUrlService::makeShortUrl($schema, $host, $statistic['code']);
        }

        return $statistic;
    }
}