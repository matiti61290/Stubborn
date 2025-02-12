<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\CartService;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
// use App\Entity\Product;

final class PaymentController extends AbstractController
{
    private ParameterBagInterface $parameterBag;

    
    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }


    #[Route('/checkout', name: 'payment', methods:['POST'])]
    public function createPaymentSession( CartService $cartService, ProductRepository $productRepository): JsonResponse
    {  
        // Récupérer les données du panier
        $stripeSecretKey = 'sk_test_51QrLVw06yik9SbPi8oBV13wgEu01YLioGv0oonVZxYVfq9pcjuQnDbtDO0oPJK5aNBaDCbRKXx7t8jSHlZulImLY00uroNqbi2';
        $cart = $cartService->getCart(); // Supposons que le service retourne votre structure de panier
        $cartWithDetails = [];
        $lineItems = [];
        $totalPrice = $cartService->getTotalPrice() * 100;

        // Construire les détails du panier avec les informations du produit
        foreach ($cart as $item) {
            $product = $productRepository->find($item['productId']);
            if ($product) {
                // Ajouter les détails du panier pour Stripe
                $cartWithDetails[] = [
                    'product' => $product,
                    'size' => $item['size'],
                    'quantity' => $item['quantity'],
                ];

                // Formater les données pour Stripe
                $lineItems[] = [
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => $product,
                            'description' => 'Taille : ' . $item['size'], // Ajoutez des détails comme la taille ici
                        ],
                        'unit_amount' => $product->getPrice() * 100, // Prix en centimes
                    ],
                    'quantity' => $item['quantity'],
                ];
            }
        }

        // Configuration de Stripe
            Stripe::setApiKey($stripeSecretKey);

        try {
            // Créer la session Stripe Checkout
            // $session = Session::create([
            //     'payment_method_types' => ['card'],
            //     'line_items' => ,
            //     'mode' => 'payment',
            //     'success_url' => 'https://example.com/success',
            //     'cancel_url' => 'https://example.com/cancel',
            // ]);
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'Total',
                        ],
                        'unit_amount' => $totalPrice, // 10,00 €
                    ],
                    'quantity' => 1,
                ]],
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
