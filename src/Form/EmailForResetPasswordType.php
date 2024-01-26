<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class EmailForResetPasswordType extends AbstractType
{
    //formulaire pour recupere l'email de l'utilisateur qui a perdu son mdp
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('submit', SubmitType::class)
        ;
    }

}
