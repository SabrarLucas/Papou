<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;

class SupplierProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
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
                        'maxMessage' => 'Votre nom est trop long',
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
                        'maxMessage' => 'Votre prenom est trop long',
                    ]),
                ]
            ])
            ->add('company_name', TextType::class, [
                'label' => 'nom de la structure',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer le nom de votre entreprise'
                    ]),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'le nom de votre entreprise est trop court',
                        'max' => 50,
                        'maxMessage' => 'le nom de votre entreprise est trop long',
                    ]),
                ]
            ])
            ->add('email', EmailType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre adresse mail'
                    ]),
                    new Length([
                        'min' => 7,
                        'minMessage' => 'Votre adresse mail est trop court',
                        'max' => 180,
                        'maxMessage' => 'Votre adresse mail est trop long',
                    ]),
                ]
            ])
        ;
    }
}
