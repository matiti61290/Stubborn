<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cart', name:'cart_')]
class CartController extends AbstractController
{
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

    #[Route('/', name:'show')]
    public function show(CartService $cartService, ProductRepository $productRepository): Response
    {
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
        ]);
    }


    #[Route('/remove/{id}/{size}', name: 'remove')]
    public function remove(int $id, string $size, CartService $cartService): Response
    {
       $cartService->removeProduct($id, $size);
       return $this->redirectToRoute('cart_show'); 
    }
}
