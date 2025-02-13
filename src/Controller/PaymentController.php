<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\CartService;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Product;

final class PaymentController extends AbstractController
{

    #[Route('/checkout', name: 'payment', methods:['POST'])]
    public function createPaymentSession( CartService $cartService, ProductRepository $productRepository, Product $product): JsonResponse
    {  
        // Récupérer les données du panier
        $stripeSecretKey = 'sk_test_51QrLVw06yik9SbPi8oBV13wgEu01YLioGv0oonVZxYVfq9pcjuQnDbtDO0oPJK5aNBaDCbRKXx7t8jSHlZulImLY00uroNqbi2';
        $cart = $cartService->getCart();

        foreach ($cart as $item) {
            $product = $productRepository->find($item['productId']);
            if($product) {
                $cartWithDetails[] = [
                    'product' => $product,
                    'size' => $item['size'],
                    'quantity' => $item['quantity']
                ];
            }
        }



        // Configuration de Stripe
            Stripe::setApiKey($stripeSecretKey);

        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => array_map(function ($cartItem) {
                    $product = $cartItem['product']; // $product est une instance de Product
                    return [
                        'price_data' => [
                            'currency' => 'eur',
                            'product_data' => [
                                'name' => $product->getName(), // Utilise le getter pour accéder au nom
                            ],
                            'unit_amount' => $product->getPrice() * 100, // Prix en centimes
                        ],
                        'quantity' => $cartItem['quantity'], // Quantité
                    ];
                }, $cartWithDetails),
                'mode' => 'payment',
                'success_url' => 'https://example.com/success',
                'cancel_url' => 'https://example.com/cancel',
            ]);

            // Retourner la session ID pour le frontend
            return new JsonResponse(['id' => $session->id]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }
}
