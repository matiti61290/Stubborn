<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\CartService;
use App\Service\StripeService;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Entity\Product;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class PaymentController extends AbstractController
{

    private StripeService $stripeService;
    private UrlGeneratorInterface $urlGenerator;

    public function __construct(StripeService $stripeService, UrlGeneratorInterface $urlGenerator)
    {
        $this->stripeService = $stripeService;
        $this->urlGenerator = $urlGenerator;
    }

    #[Route('/checkout', name: 'payment', methods:['POST'])]
    public function createPaymentSession( CartService $cartService, ProductRepository $productRepository, Product $product): JsonResponse
    {
        $stripeSecretKey = $this->stripeService->getStripeSecretKey();
        $successUrl = $this->urlGenerator->generate('app_home', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $cancelUrl = $this->urlGenerator->generate('checkout_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $cart = $cartService->getCart();

        //retrieve products in the cart
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

        // Stripe config
        Stripe::setApiKey($stripeSecretKey);

        //Payment session creation try
        try {
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => array_map(function ($cartItem) {
                    $product = $cartItem['product']; 
                    return [
                        'price_data' => [
                            'currency' => 'eur',
                            'product_data' => [
                                'name' => $product->getName(), 
                            ],
                            'unit_amount' => $product->getPrice() * 100, //to convert centime in euro
                        ],
                        'quantity' => $cartItem['quantity'], 
                    ];
                }, $cartWithDetails),
                'mode' => 'payment',
                'success_url' => $successUrl,
                'cancel_url' => $cancelUrl,
            ]);
            $cartService->clearCart();

            return new JsonResponse(['id' => $session->id]);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => $e->getMessage()], 500);
        }
    }

    #[Route('/checkout/cancel', name:"checkout_cancel")]
    public function checkoutCancel(): Response
    {
        return $this->render('checkout/errorCheckout.html.twig');
    }
}