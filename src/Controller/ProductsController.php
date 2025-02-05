<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Product;
use App\Entity\Size;

final class ProductsController extends AbstractController
{
    #[Route('/sweatshirts', name: 'products')]
    public function productsAll(ProductRepository $productRepository): Response
    {

        $products = $productRepository->findAllProducts();

        return $this->render('products/productsList.html.twig', [
            'controller_name' => 'ProductsController',
            'products' => $products,
        ]);
    }

    #[Route('/sweatshirts/{id}', name:'product_detail')]
    public function productDetails(?Product $product, EntityManagerInterface $em): Response
    {
        $sizes = $em->getRepository(Size::class)->findAll();
        return $this->render('products/product.html.twig',[
            'product' => $product,
            'sizes'=> $sizes
        ]);
    }
}