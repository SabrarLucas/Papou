<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EmailForResetPasswordType extends AbstractType
{
    //formulaire pour recupere l'email de l'utilisateur qui a perdu son mdp
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
            ->add('submit', SubmitType::class)
        ;
    }

}
