<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;

class CartService
{
    private $session;

    public function __construct(RequestStack $requestStack)
    {
        $this->session = $requestStack->getSession();
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
}