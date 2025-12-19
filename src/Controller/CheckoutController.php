<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'app_checkout')]
    public function checkout(CartService $cart): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $data = $cart->getDetailed();
        if (count($data['items']) === 0) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('app_shop_index');
        }

        return $this->render('checkout/checkout.html.twig', [
            'items' => $data['items'],
            'totalCents' => $data['totalCents'],
        ]);
    }

    #[Route('/checkout/confirm', name: 'app_checkout_confirm', methods: ['POST'])]
    public function confirm(CartService $cart, EntityManagerInterface $em): RedirectResponse
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $data = $cart->getDetailed();
        if (count($data['items']) === 0) {
            $this->addFlash('warning', 'Votre panier est vide.');
            return $this->redirectToRoute('app_shop_index');
        }

        $order = new Order();
        $order->setCustomer($this->getUser());
        $order->setStatus('paid_simulated');
        $order->setTotalCents($data['totalCents']);

        foreach ($data['items'] as $row) {
            $item = new OrderItem();
            $item->setProduct($row['product']);
            $item->setQuantity($row['qty']);
            $item->setUnitPriceCents($row['product']->getPriceCents());
            $order->addItem($item);
        }

        $em->persist($order);
        $em->flush();

        $cart->clear();

        $this->addFlash('success', 'Commande validée (paiement simulé).');
        return $this->redirectToRoute('app_order_show', ['id' => $order->getId()]);
    }
}
