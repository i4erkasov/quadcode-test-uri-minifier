<?php

namespace App\Requests;

use App\Interfaces\RequestDtoInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class UrlGenerateRequest implements RequestDtoInterface
{
    /**
     * @Assert\NotNull(
     *     message="Url не может быть пустым 2"
     * )
     * @Assert\NotBlank(
     *     message="Url не может быть пустым 1"
     * )
     * @Assert\Url(
     *     message="Не верный формат url"
     * )
     */
    private ?string $url;

    private string $host;

    private string $schema;

    private int $port;

    /**
     * UrlGenerateRequest constructor.
     *
     * @param Request $request
     */
    public function __construct(Request $request)
    {
        $this->schema = $request->getScheme();
        $this->host = $request->getHost();
        $this->port   = $request->getPort();

        $content = json_decode($request->getContent(), true);

        $this->url = $content['url'] ?? null;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getHost(): string
    {
        if (!in_array($this->port, [80, 443])) {
            $this->host .= ':' . $this->port;
        }

        return $this->host;
    }

    /**
     * @return string
     */
    public function getSchema(): string
    {
        return $this->schema;
    }

    /**
     * @return int
     */
    public function getPort(): int
    {
        return $this->port;
    }
}