<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Size;
use App\Entity\Stock;
use App\Form\SweatshirtType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class SweatshirtController extends AbstractController
{
    //display each product in the database and the creation form
    #[Route('/admin', name: 'admin')]
    public function list(EntityManagerInterface $em, Request $request, SluggerInterface $slugger): Response
    {
        //retrieve products from the database
        $products = $em->getRepository(Product::class)->findAll();
    
        $newProduct = new Product();
        $sizes = $em->getRepository(Size::class)->findAll();
    
        foreach ($sizes as $size) {
            $stock = new Stock();
            $stock->setSize($size);
            $newProduct->addStock($stock);
        }
        
        //Form logic
        $createForm = $this->createForm(SweatshirtType::class, $newProduct, [
            'is_edit' => false,
        ]);
        $createForm->handleRequest($request);
    
        if ($createForm->isSubmitted() && $createForm->isValid()) {
            $imageFile = $createForm->get('image')->getData();
    
            if (!$imageFile) {
                $this->addFlash('error', 'L\'image est obligatoire pour créer un produit.');
                return $this->redirectToRoute('admin');
            }
    
            $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $slugger->slug($originalFilename);
            $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
    
            try {
                $imageFile->move($this->getParameter('uploads_directory'), $newFilename);
            } catch (FileException $e) {
                $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                return $this->redirectToRoute('admin');
            }
    
            $newProduct->setImage($newFilename);
            $em->persist($newProduct);
            $em->flush();
    
            $this->addFlash('success', 'Produit créé avec succès.');
            return $this->redirectToRoute('admin');
        }
    
        $editForms = [];
        foreach ($products as $product) {
            $editForms[$product->getId()] = $this->createForm(SweatshirtType::class, $product, [
                'is_edit' => true,
            ])->createView();
        }
    
        return $this->render('admin/list.html.twig', [
            'products' => $products,
            'createForm' => $createForm->createView(),
            'editForms' => $editForms
        ]);
    }
    
    #[Route('/admin/sweatshirt/{id}/edit', name: 'sweatshirt_edit')]
    public function edit(Product $product, Request $request, EntityManagerInterface $em, SluggerInterface $slugger): Response
    {
        //Edit form logic
        $form = $this->createForm(SweatshirtType::class, $product, [
            'is_edit' => true,
        ]);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('image')->getData();
    
            if ($imageFile) {
                $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $imageFile->guessExtension();
    
                try {
                    $imageFile->move($this->getParameter('uploads_directory'), $newFilename);
                } catch (FileException $e) {
                    $this->addFlash('error', 'Erreur lors de l\'upload de l\'image.');
                    return $this->redirectToRoute('admin');
                }
    
                $product->setImage($newFilename);
            }
    
            $em->flush();
            $this->addFlash('success', 'Produit mis à jour avec succès.');
        } else {
            $this->addFlash('error', 'Erreur lors de la mise à jour du produit.');
        }
    
        return $this->redirectToRoute('admin');
    }
    

    #[Route('/admin/sweatshirt/{id}/delete', name: 'sweatshirt_delete', methods: ['POST'])]
    public function delete(Product $product, EntityManagerInterface $em, Request $request): Response
    {
        //delete a product in the database
        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $filesystem = new Filesystem();
    
            if ($product->getImage()) {
                $imagePath = $this->getParameter('uploads_directory') . '/' . $product->getImage();
                if ($filesystem->exists($imagePath)) {
                    $filesystem->remove($imagePath);
                }
            }
    
            $em->remove($product);
            $em->flush();
    
            $this->addFlash('success', 'Le sweatshirt a été supprimé avec succès.');
        }
    
        return $this->redirectToRoute('admin');
    }
}