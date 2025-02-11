<?php

namespace App\Form;

use App\Entity\Stock;
use App\Entity\Size;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StockType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('quantity', IntegerType::class, [
                'required' => true,
                'label' => false,
            ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $stock = $form->getData();
        if ($stock && $stock->getSize()) {
            $view->vars['size_label'] = $stock->getSize()->getSizeLabel(); // Récupère le nom de la taille
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Stock::class,
        ]);
    }
}