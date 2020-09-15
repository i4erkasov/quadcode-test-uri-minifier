<?php

namespace App\Entity;

use App\Repository\ShortUrlsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="short_urls")
 * @ORM\Entity(repositoryClass=ShortUrlsRepository::class)
 */
class ShortUrl
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     *
     * @ORM\OneToMany(targetEntity="App\Entity\StatisticRedirect", mappedBy="url_id")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=2048, unique=true)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=10, unique=true)
     */
    private $code;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     */
    private $createdAt;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
