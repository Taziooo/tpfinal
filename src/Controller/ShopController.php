<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ShopController extends AbstractController
{
    #[Route('/shop', name: 'app_shop_index')]
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('shop/index.html.twig', [
            'products' => $productRepository->findActive(),
        ]);
    }

    #[Route('/shop/{id}', name: 'app_shop_show', requirements: ['id' => '\d+'])]
    public function show(int $id, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($id);
        if (!$product || !$product->isActive()) {
            throw $this->createNotFoundException();
        }

        return $this->render('shop/show.html.twig', [
            'product' => $product,
        ]);
    }
}
