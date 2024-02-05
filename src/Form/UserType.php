<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class, [
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer votre adresse mail'
                ]),
                new Length([
                    'min' => 7,
                    'minMessage' => 'Votre adresse mail est trop court',
                    'max' => 180,
                ]),
            ]
        ])
        ->add('firstname', TextType::class, [
            'label' => 'Prenom',
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer votre prenom'
                ]),
                new Length([
                    'min' => 3,
                    'minMessage' => 'Votre prenom est trop court',
                    'max' => 40,
                ]),
            ]
        ])
        ->add('lastname', TextType::class, [
            'label' => 'Nom',
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer votre nom'
                ]),
                new Length([
                    'min' => 3,
                    'minMessage' => 'Votre nom est trop court',
                    'max' => 40,
                ]),
            ]
        ])
        ->add('address', TextType::class, [
            'label' => 'Adresse',
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer votre adresse'
                ]),
                new Length([
                    'min' => 5,
                    'minMessage' => 'Votre adresse est trop court',
                    'max' => 100,
                ]),
            ]
        ])
        ->add('zipcode', TextType::class, [
            'label' => 'Code postal',
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer votre code postal'
                ]),
                new Length([
                    'min' => 5,
                    'minMessage' => 'Votre code postal est trop court',
                    'max' => 5,
                ]),
            ]
        ])
        ->add('city', TextType::class, [
            'label' => 'Ville',
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer votre ville'
                ]),
                new Length([
                    'min' => 1,
                    'minMessage' => 'Votre ville est trop court',
                    'max' => 105,
                ]),
            ]
        ])
        ->add('country', TextType::class, [
            'label' => 'Pays',
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer pays'
                ]),
                new Length([
                    'min' => 5,
                    'minMessage' => 'Votre pays est trop court',
                    'max' => 50,
                ]),
            ]
        ])
        ->add('phone', TelType::class, [
            'label' => 'Téléphone'
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