<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProductRepository;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(ProductRepository $productRepository): Response
    {

        $highlightedProducts = $productRepository->findAllHighlightedProducts();

        return $this->render('home/home.html.twig', [
            'controller_name' => 'HomeController',
            'user' => $this->getUser(),
            'highlightedProducts'=> $highlightedProducts
        ]);
    }
}
