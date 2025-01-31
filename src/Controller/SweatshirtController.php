<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Size;
use App\Entity\Stock;
use App\Form\SweatshirtType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class SweatshirtController extends AbstractController
{
    #[Route('/sweatshirts', name: 'sweatshirts')]
    public function list(EntityManagerInterface $em): Response
    {
        $products = $em->getRepository(Product::class)->findAll();

        return $this->render('sweatshirt/list.html.twig', [
            'products' => $products,
        ]);
    }

    #[Route('sweatshirt/{id}', name: 'sweatshirt_details')]
    public function sweatshirtDetails(?Product $product, EntityManagerInterface $em): Response
    {
        $sizes = $em->getRepository(Size::class)->findAll();
        return $this->render('sweatshirt/sweatshirt.html.twig', [
            'product' => $product,
            'sizes' => $sizes
        ]);
    }

    #[Route('/new', name: 'sweatshirt_new')]
    #[Route('/sweatshirt/{id}/edit', name: 'sweatshirt_edit')]
    public function form(Product $product = null, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        if (!$product) {
            $product = new Product();
        }

        // Récupérer toutes les tailles disponibles
        $sizes = $em->getRepository(Size::class)->findAll();

        // Ajouter un stock pour chaque taille dans le produit
        foreach ($sizes as $size) {
            $existingStock = $product->getStocks()->filter(function ($stock) use ($size) {
                return $stock->getSize() === $size;
            })->first();
    
            if (!$existingStock) {
                $stock = new Stock();
                $stock->setSize($size);
                $product->addStock($stock);
            }
        }

        // Création du formulaire
        $form = $this->createForm(SweatshirtType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();

            if($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$imageFile->guessExtension();

                try {
                    $imageFile->move(
                        $this->getParameter('uploads_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {

                }
                $product->setImage($newFilename);

            }
            
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('sweatshirts');
        }

        return $this->render('sweatshirt/form.html.twig', [
            'form' => $form->createView(),
            'sizes' => $sizes,
            'product' => $product
        ]);
    }
}