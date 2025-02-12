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

    public function getCart(): array
    {
        return $this->session->get('cart', []);
    }

    public function removeProduct(int $productId, string $size): void
    {
        $cart = $this->session->get('cart', []);

        $key = $productId . '_' . $size;
        if (isset($cart[$key])) {
            unset($cart[$key]);
        }

        $this->session->set('cart', $cart);
    }

    public function clearCart(): void
    {
        $this->session->remove('cart');
    }

    public function getProductRepository(): ProductRepository
    {
        return $this->productRepository;
    }
}