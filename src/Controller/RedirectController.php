<?php

namespace App\Controller;

use App\Services\ShortUrlService;
use App\Services\StatisticRedirectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class RedirectController extends AbstractController
{
    /**
     * @param string                   $code
     * @param ShortUrlService          $shortUrlService
     * @param StatisticRedirectService $statisticService
     *
     * @throws \Throwable
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function index(string $code, ShortUrlService $shortUrlService, StatisticRedirectService $statisticService)
    {
        $shortUrl = $shortUrlService->getShortUrlByCode($code);

        if ($shortUrl) {
            $statisticService->registrationRedirect($shortUrl->getId());

            return $this->redirect($shortUrl->getUrl(), 302);
        }

        return new Response('The server returned a "404 Not Found."', 404);
    }
}