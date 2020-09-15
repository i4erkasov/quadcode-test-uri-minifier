<?php

namespace App\Requests;

use App\Interfaces\RequestDtoInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints as Assert;

class StatisticRedirectRequest implements RequestDtoInterface
{
    /**
     * @Assert\NotNull(
     *     message="Url не может быть пустым 2"
     * )
     * @Assert\NotBlank(
     *     message="Url не может быть пустым 1"
     * )
     */
    private int $page;

    /**
     * @Assert\NotNull(
     *     message="Url не может быть пустым 2"
     * )
     * @Assert\NotBlank(
     *     message="Url не может быть пустым 1"
     * )
     */
    private int $limit;

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
        $this->host   = $request->getHost();
        $this->port   = $request->getPort();
        $this->limit  = (int)$request->get('limit', 10);
        $this->page   = (int)$request->get('page', 1);
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getPage(): int
    {
        return $this->page;
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
    public function getScheme(): string
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