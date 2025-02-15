<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use App\Repository\ProductRepository;

class CartService
{
    private $session;
    private $productRepository;

    public function __construct(RequestStack $requestStack, ProductRepository $productRepository)
    {
        $this->session = $requestStack->getSession();
        $this->productRepository = $productRepository;
    }

    //Calcul the total price of the cart
    public function getTotalPrice(): float
    {
        $cart = $this->session->get('cart', []);
        $total = 0;

        foreach ($cart as $item) {
            $product = $this->productRepository->find($item['productId']);

            if ($product) {
                $total += $product->getPrice() * $item['quantity'];
            }
        }

        return $total;
    }

    //Add a product in the cart
    public function addProduct(int $productId, string $size): void
    {
        $cart = $this->session->get('cart', []);

        $key = $productId . '_' . $size;

        if (!isset($cart[$key])) {
            $cart[$key] = [
                'productId' => $productId,
                'size' => $size,
                'quantity' => 1
            ];
        } else {
            $cart[$key]['quantity']++;
        }

        $this->session->set('cart', $cart);
    }

    //get products in the cart session
    public function getCart(): array
    {
        return $this->session->get('cart', []);
    }

    //remove product in the cart session
    public function removeProduct(int $productId, string $size): void
    {
        $cart = $this->session->get('cart', []);

        $key = $productId . '_' . $size;
        if (isset($cart[$key])) {
            unset($cart[$key]);
        }

        $this->session->set('cart', $cart);
    }

    //clear the cart
    public function clearCart(): void
    {
        $this->session->remove('cart');
    }
}