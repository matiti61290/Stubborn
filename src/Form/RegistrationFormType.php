<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => "Nom d'utilisateur :",
                'constraints' => [
                    new NotBlank(['message'=> 'Le nom est obligatoire.']),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'Le nom doit contenir au moins 2 caracteres',
                        'max' => 50,
                        'maxMessage' => 'Le nom ne peut contenir plus de 50 caracteres'
                    ]),
                ],
                'attr' => [
                  'class' => 'd-flex flex-row mb-2 pt-1 border border-dark-subtle'  
                ],
            ])
            ->add('email', TextType::class, [
                'label' => 'Adresse mail :',
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'L\'email est obligatoire.',
                    ]),
                    new Email(
                        message: 'Veuillez entrer une adresse mail valide.',
                        mode: 'strict' 
                    ),
                    new Length(
                        max: 180,
                        maxMessage: "l'email ne doit pas depasser 180 caracteres."
                    )
                    ],
                'attr' => [
                    'class' => 'd-flex flex-row py-1 border border-dark-subtle'  
                    ],
            ])
            ->add('deliveryAddress', TextType::class, [
                'label' => "Adresse de livraison",
                'required' => true,
                'constraints' => [
                    new Length([
                        'min' => 5,
                        'minMessage' => "L'adresse doit comporter au moins 5 caracteres.",
                        'max' => 255,
                        'maxMessage' => "L'adresse ne peut pas depasser 255 caracteres"
                    ]),
                    new Regex(
                        pattern: "/^[0-9a-zA-ZÀ-ÖØ-öø-ÿ\s,'\-]+$/",
                        message: "Le format de l'adresse comporte des caracteres invalide."
                    )
                ],
                'attr' => [
                    'class' => 'd-flex flex-row py-1 border border-dark-subtle'  
                  ],
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent etre identiques',
                'options' => [
                    'attr' => ['class' => 'd-flex flex-column border border-dark-subtle'
                ]],
                'required' => true,
                'first_options' => ['label' => 'Mot de passe',
                                        'attr' => ['class' => 'd-flex flex-column border border-dark-subtle py-1']],
                'second_options' => ['label' => 'Confirmer le mot de passe :',
                'attr' => ['class' => 'd-flex flex-column border border-dark-subtle py-2']],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un mot de passe.',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit comporter {{ limit }} caracteres.',
                        // max length allowed by Symfony for security reasons
                        'max' => 64,
                        'maxMessage' => 'Le mot de passe ne doit pas depasser 64 caracteres.'
                    ]),
                    new Regex(
                        pattern: "/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,64}$/",
                        message: "Le mot de passe doit contenir au moins une majuscule, un chiffre et un caractere special."
                    )
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
