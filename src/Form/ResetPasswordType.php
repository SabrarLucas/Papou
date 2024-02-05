<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResetPasswordType extends AbstractType
{
    //formulaire de creation d'un nouveau mdp
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('password', RepeatedType::class,[ //avec RepeatedType les deux option doit etre identique
                'type' => PasswordType::class,
                'first_options' => [
                    'attr' => [
                        'placeholder' => 'Veuillez entrer votre nouveau mot de passe',
                    ],
                    'label' => 'Nouveau mot de passe',
                ],
                'second_options' => [
                    'attr' => [
                        'placeholder' => 'Veuillez confirmer votre nouveau mot de passe',
                    ],
                    'label' => 'Confirmation du nouveau mot de passe',
                ],
                'invalid_message' => 'Les mots de passe ne correspondent pas'  // Message si les mdp 1 et 2 ne sont pas identiques
            ])
            ->add('submit', SubmitType::class)
            
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
