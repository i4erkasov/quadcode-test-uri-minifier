<?php

namespace App\Controller;

use App\Requests\UrlGenerateRequest;
use App\Services\ShortUrlService;

class ShortUrlController
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
        $port   = $request->getPort();
        $url    = $request->getUrl();

        if (in_array($port, [80, 443])) {
            $host .= ':' . $port;
        }

        return [
            'short_url' => $service->createShortUrl($schema, $host, $url),
        ];
    }

    /**
     * @param int             $length
     * @param ShortUrlService $service
     *
     * @return array
     */
    public function limits(int $length, ShortUrlService $service)
    {
        return [
            'limit' => $service->getShorteningLimit($length),
        ];
    }
}