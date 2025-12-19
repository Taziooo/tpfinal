<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    #[Route('/orders/{id}', name: 'app_order_show', requirements: ['id' => '\d+'])]
    public function show(int $id, OrderRepository $orders): Response
    {
        $this->denyAccessUnlessGranted('ROLE_USER');

        $order = $orders->find($id);
        if (!$order) {
            throw $this->createNotFoundException();
        }

        // sécurité : la commande doit appartenir au user
        if ($order->getCustomer()->getUserIdentifier() !== $this->getUser()->getUserIdentifier()) {
            throw $this->createAccessDeniedException();
        }

        return $this->render('order/show.html.twig', ['order' => $order]);
    }
}
