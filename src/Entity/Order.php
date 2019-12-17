<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\OrderRepository")
 * @ORM\Table(name="`order`")
 */
class Order
{
    public const STATUS_CREATED = 0;
    public const STATUS_CHARGED = 1;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(type="integer")
     */
    private int $price;

    /**
     * @ORM\Column(type="integer", options={"default"=0}, nullable=false)
     */
    private int $status = self::STATUS_CREATED;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function isNotEqualPrice(float $price): bool
    {
        return $price !== $this->getPrice();
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function isCharged(): bool
    {
        return static::STATUS_CHARGED === $this->status;
    }
}
