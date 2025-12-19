<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private const KEY = 'cart_items'; // [productId => qty]

    public function __construct(
        private RequestStack $requestStack,
        private ProductRepository $productRepository
    ) {}

    /** @return array<int,int> */
    public function getRaw(): array
    {
        $session = $this->requestStack->getSession();
        return $session->get(self::KEY, []);
    }

    private function save(array $raw): void
    {
        $this->requestStack->getSession()->set(self::KEY, $raw);
    }

    public function add(Product $product, int $qty = 1): void
    {
        $raw = $this->getRaw();
        $id = (int)$product->getId();
        $raw[$id] = ($raw[$id] ?? 0) + max(1, $qty);
        $this->save($raw);
    }

    public function setQuantity(Product $product, int $qty): void
    {
        $raw = $this->getRaw();
        $id = (int)$product->getId();
        if ($qty <= 0) {
            unset($raw[$id]);
        } else {
            $raw[$id] = $qty;
        }
        $this->save($raw);
    }

    public function remove(Product $product): void
    {
        $raw = $this->getRaw();
        unset($raw[(int)$product->getId()]);
        $this->save($raw);
    }

    public function clear(): void
    {
        $this->save([]);
    }

    /**
     * @return array{items: array<int,array{product:Product, qty:int, lineTotalCents:int}>, totalCents:int}
     */
    public function getDetailed(): array
    {
        $raw = $this->getRaw();
        $items = [];
        $total = 0;

        foreach ($raw as $productId => $qty) {
            $product = $this->productRepository->find($productId);
            if (!$product || !$product->isActive()) {
                continue;
            }
            $line = $product->getPriceCents() * $qty;
            $items[] = [
                'product' => $product,
                'qty' => $qty,
                'lineTotalCents' => $line,
            ];
            $total += $line;
        }

        return ['items' => $items, 'totalCents' => $total];
    }
}
