<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductRepository;

final class ProductsController extends AbstractController
{
    #[Route('/sweatshirts', name: 'app_sweatshirts')]
    public function index(ProductRepository $productRepository): Response
    {

        $products = $productRepository->findAllProducts();

        return $this->render('products/productsList.html.twig', [
            'controller_name' => 'ProductsController',
            'products' => $products,
        ]);
    }
}
