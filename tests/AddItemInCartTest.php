<?php
namespace App\Tests\Controller;

use App\Service\CartService;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CartControllerTest extends WebTestCase
{
    public function testAddProductToCart(): void
    {
        $client = static::createClient();

        // Mock du ProductRepository
        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->method('find')
            ->willReturn((object) ['id' => 1]);

        // Mock du CartService
        $cartService = $this->createMock(CartService::class);
        $cartService->expects($this->once())
            ->method('addProduct')
            ->with($this->equalTo(1), $this->equalTo('M'));

        // Remplacement des services dans le conteneur
        $client->getContainer()->set('App\Repository\ProductRepository', $productRepository);
        $client->getContainer()->set('App\Service\CartService', $cartService);

        // Envoi d'une requête vers la route
        $client->request('GET', '/cart/add/1?size=M');

        // Vérification de la redirection
        $this->assertResponseRedirects($this->getResult('cart_show'));

        // Suivi de la redirection
        $client->followRedirect();

        // Vérifiez que la redirection aboutit à la bonne page (optionnel)
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Votre panier'); // Exemple, ajustez selon le contenu de votre page
    }

    public function testAddProductNotFound(): void
    {
        $client = static::createClient();

        // Mock du ProductRepository qui retourne null
        $productRepository = $this->createMock(ProductRepository::class);
        $productRepository->method('find')
            ->willReturn(null);

        // Remplacement du service dans le conteneur
        $client->getContainer()->set('App\Repository\ProductRepository', $productRepository);

        // Envoi d'une requête vers la route
        $client->request('GET', '/cart/add/999');

        // Vérification que l'exception 404 est levée
        $this->assertResponseStatusCodeSame(404);
    }
}
