<?php

namespace App\Tests\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\JsonResponse;

class PaymentControllerTest extends WebTestCase
{
    public function testCreatePaymentSession(): void
    {
        $client = static::createClient();

        // Création d'une instance réelle de Product
        $product = new Product();
        $product->setName('Produit Test');
        $product->setPrice(1000); // Prix en centimes
        $product->setImage('image_test.jpg');

        // Mock du ProductRepository
        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->method('find')
            ->willReturn($product); // Retourne l'instance de Product

        // Mock du CartService
        $cartService = $this->createMock(CartService::class);
        $cartService->method('getCart')
            ->willReturn([
                ['productId' => 1, 'size' => 'M', 'quantity' => 2]
            ]);
        $cartService->expects($this->once())->method('clearCart');

        // Remplacement des services dans le conteneur
        $client->getContainer()->set('App\Repository\ProductRepository', $productRepository);
        $client->getContainer()->set('App\Service\CartService', $cartService);

        // Envoi de la requête POST vers /checkout
        $client->request('POST', '/checkout', [], [], ['CONTENT_TYPE' => 'application/json']);

        // Vérification de la réponse
        $this->assertResponseIsSuccessful();
        $response = $client->getResponse();
        $this->assertInstanceOf(JsonResponse::class, $response);

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $data, 'La réponse doit contenir un ID de session Stripe.');
        $this->assertNotEmpty($data['id'], 'L\'ID de session Stripe ne doit pas être vide.');
    }
}
