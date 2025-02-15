<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[Route('/cart', name:'cart_')]
class CartController extends AbstractController
{
    private ParameterBagInterface $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    //add a product in the cart with the id
    #[Route('/add/{id}', name: 'add')]
    public function add(int $id, Request $request, ProductRepository $productRepository, CartService $cartService): Response
    {
        $product = $productRepository->find($id);
        if (!$product) {
            throw $this->createNotFoundException("Produit introuvable");
        }

        $size = $request->query->get('size', 'M');
        $cartService->addProduct($id, $size);

        return $this->redirectToRoute('cart_show');
    }

    //render each product in the cart
    #[Route('/', name:'show')]
    public function show(CartService $cartService, ProductRepository $productRepository): Response
    {
        $stripePublicKey = $this->parameterBag->get('env(STRIPE_PUBLIC_KEY)');
        $cart = $cartService->getCart();
        $cartWithDetails = [];

        foreach ($cart as $item) {
            $product = $productRepository->find($item['productId']);
            if ($product) {
                $cartWithDetails[] = [
                    'product' => $product,
                    'size' => $item['size'],
                    'quantity' => $item['quantity']
                ];
            }
        }

        $totalPrice = $cartService->getTotalPrice();

        return $this->render('cart/cart.html.twig', [
            'cart' => $cartWithDetails,
            'totalPrice' => $totalPrice,
            'stripePublicKey' => $stripePublicKey
        ]);
    }

    //delete a product in the cart
    #[Route('/remove/{id}/{size}', name: 'remove')]
    public function remove(int $id, string $size, CartService $cartService): Response
    {
       $cartService->removeProduct($id, $size);
       return $this->redirectToRoute('cart_show'); 
    }
}
