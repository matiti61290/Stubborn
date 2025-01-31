<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('image', FileType::class, [
            'label' => 'Image du produit (PNG, JPG)',
            'mapped' => false, // ne pas lier directement à l'entité Product
            'required' => false,
            'attr' => [
                'accept' => 'image/png, image/jpeg', // restreindre les types de fichiers
            ]
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
    
    }
}