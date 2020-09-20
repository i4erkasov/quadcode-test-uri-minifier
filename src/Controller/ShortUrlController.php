<?php

namespace App\Controller;

use App\Requests\UrlGenerateRequest;
use App\Services\ShortUrlService;
use App\Services\StringGeneratorService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ShortUrlController extends AbstractController
{
    /**
     * @param UrlGenerateRequest $request
     * @param ShortUrlService    $service
     *
     * @throws \Throwable
     * @return array
     */
    public function generate(UrlGenerateRequest $request, ShortUrlService $service)
    {
        $schema = $request->getSchema();
        $host   = $request->getHost();
        $url    = $request->getUrl();

        return $service->createShortUrl($schema, $host, $url);
    }

    /**
     * @param int             $length
     * @param ShortUrlService $service
     *
     * @throws \Doctrine\DBAL\DBALException
     * @return array
     */
    public function limits(int $length, ShortUrlService $service)
    {
        return [
            'limit' => $service->getShorteningLimit($length),
        ];
    }
}