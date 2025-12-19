<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CartController extends AbstractController
{
    #[Route('/cart', name: 'app_cart_index')]
    public function index(CartService $cart): Response
    {
        $data = $cart->getDetailed();
        return $this->render('cart/index.html.twig', [
            'items' => $data['items'],
            'totalCents' => $data['totalCents'],
        ]);
    }

    #[Route('/cart/add/{id}', name: 'app_cart_add', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function add(int $id, Request $request, ProductRepository $products, CartService $cart): RedirectResponse
    {
        $product = $products->find($id);
        if (!$product || !$product->isActive()) {
            throw $this->createNotFoundException();
        }

        $qty = (int)($request->request->get('qty', 1));
        $cart->add($product, $qty);
        $this->addFlash('success', 'Produit ajouté au panier.');

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/cart/update/{id}', name: 'app_cart_update', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function update(int $id, Request $request, ProductRepository $products, CartService $cart): RedirectResponse
    {
        $product = $products->find($id);
        if (!$product) {
            throw $this->createNotFoundException();
        }

        $qty = (int)($request->request->get('qty', 1));
        $cart->setQuantity($product, $qty);
        $this->addFlash('success', 'Panier mis à jour.');

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/cart/remove/{id}', name: 'app_cart_remove', requirements: ['id' => '\d+'], methods: ['POST'])]
    public function remove(int $id, ProductRepository $products, CartService $cart): RedirectResponse
    {
        $product = $products->find($id);
        if ($product) {
            $cart->remove($product);
        }
        $this->addFlash('success', 'Produit supprimé du panier.');

        return $this->redirectToRoute('app_cart_index');
    }

    #[Route('/cart/clear', name: 'app_cart_clear', methods: ['POST'])]
    public function clear(CartService $cart): RedirectResponse
    {
        $cart->clear();
        $this->addFlash('success', 'Panier vidé.');
        return $this->redirectToRoute('app_cart_index');
    }
}
