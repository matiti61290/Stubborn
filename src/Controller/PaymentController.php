<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\CartService;

final class PaymentController extends AbstractController
{
    #[Route('/payment', name: 'payment')]
    public function index(CartService $cartService, ProductRepository $productRepository): Response
    {  
        $totalPrice = $cartService->getTotalPrice();

        return $this->render('payment/index.html.twig',[
            'totalPrice' => $totalPrice,
        ]);
    }
}
