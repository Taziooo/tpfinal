<?php

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Order $orderRef = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Product $product = null;

    #[ORM\Column]
    private int $quantity = 1;

    // snapshot prix à l'achat (centimes)
    #[ORM\Column]
    private int $unitPriceCents = 0;

    public function getId(): ?int { return $this->id; }

    public function getOrderRef(): ?Order { return $this->orderRef; }
    public function setOrderRef(?Order $orderRef): self { $this->orderRef = $orderRef; return $this; }

    public function getProduct(): ?Product { return $this->product; }
    public function setProduct(Product $product): self { $this->product = $product; return $this; }

    public function getQuantity(): int { return $this->quantity; }
    public function setQuantity(int $quantity): self { $this->quantity = max(1, $quantity); return $this; }

    public function getUnitPriceCents(): int { return $this->unitPriceCents; }
    public function setUnitPriceCents(int $unitPriceCents): self { $this->unitPriceCents = $unitPriceCents; return $this; }

    public function getLineTotalCents(): int
    {
        return $this->unitPriceCents * $this->quantity;
    }

    public function getLineTotalEuros(): string
    {
        return number_format($this->getLineTotalCents() / 100, 2, ',', ' ');
    }
}
