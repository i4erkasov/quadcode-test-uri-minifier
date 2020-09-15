<?php

namespace App\Entity;

use App\Repository\StatisticRedirectRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=StatisticRedirectRepository::class)
 */
class StatisticRedirect
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", name="url_id")
     *
     * @ORM\ManyToOne(targetEntity="ShortUrl", inversedBy="id")
     */
    private $urlId;

    /**
     * @ORM\Column(type="datetime", name="redirect_datetime")
     */
    private $redirectDatetime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrlId(): ?int
    {
        return $this->urlId;
    }

    public function setUrlId(int $urlId): self
    {
        $this->urlId = $urlId;

        return $this;
    }

    public function getRedirectDatetime(): ?\DateTimeInterface
    {
        return $this->redirectDatetime;
    }

    public function setRedirectDatetime(\DateTimeInterface $redirectDatetime): self
    {
        $this->redirectDatetime = $redirectDatetime;

        return $this;
    }
}
