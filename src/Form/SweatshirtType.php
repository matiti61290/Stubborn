<?php

namespace App\Form;

use App\Entity\Product;
use App\Form\StockType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

use Symfony\Component\Validator\Constraints\Image;


class SweatshirtType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom du produit',
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix',
            ])
            ->add('highlighted', CheckboxType::class, [
                'label' => 'Le sweat est-il mis en avant?',
                'required' => False,
            ])
            ->add('image', FileType::class, [
                'label'=> 'Image du produit (jpeg/png)',
                'mapped'=> false,
                'required' => $options['is_edit'] ? false : true,
                'constraints' => [
                    new Image([
                        'maxSize' => '10M',
                        'mimeTypes' => ['image/jpeg', 'image/png'],
                        'mimeTypesMessage' => 'Veuillez télécharger une image valide (jpeg ou png)'
                    ])
                ]
            ])
            ->add('stocks', CollectionType::class, [
                'entry_type' => StockType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Stocks',
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Enregistrer',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'is_edit' => false
        ]);
    }
}