<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Size;
use App\Entity\Stock;
use App\Form\SweatshirtType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SweatshirtController extends AbstractController
{
    #[Route('/sweatshirt/new', name: 'sweatshirt_new')]
    #[Route('/sweatshirt/{id}/edit', name: 'sweatshirt_edit')]
    public function form(Product $product = null, Request $request, EntityManagerInterface $em): Response
    {
        if (!$product) {
            $product = new Product();
        }

        // Création du formulaire
        $form = $this->createForm(SweatshirtType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer toutes les tailles existantes
            $sizes = $em->getRepository(Size::class)->findAll();

            // Vérifier si les stocks existent déjà, sinon les créer
            foreach ($sizes as $size) {
                $existingStock = $em->getRepository(Stock::class)->findOneBy([
                    'product' => $product,
                    'size' => $size
                ]);

                if (!$existingStock) {
                    $stock = new Stock();
                    $stock->setProduct($product);
                    $stock->setSize($size);
                    $stock->setQuantity(0); // Initialiser à 0 si absent
                    $em->persist($stock);
                }
            }

            $em->persist($product);
            $em->flush(); // Assurer que toutes les tailles sont présentes

            return $this->redirectToRoute('sweatshirt_list');
        }

        return $this->render('sweatshirt/form.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/sweatshirts', name: 'sweatshirt_list')]
    public function list(EntityManagerInterface $em): Response
    {
        $products = $em->getRepository(Product::class)->findAll();

        return $this->render('sweatshirt/list.html.twig', [
            'products' => $products,
    ]);
}
}