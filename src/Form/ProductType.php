<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Supplier;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un nom au produit'
                    ]),
                    new Length([
                        'min' => 5,
                        'max' =>  100
                    ])
                ]
            ])
            ->add('description', TextareaType::class)
            ->add('price', MoneyType::class, [
                'label' => 'Prix',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un prix au produit'
                    ]),
                    new Length([
                        'max' => 9,
                    ])
                ]
            ])
            ->add('age', ChoiceType::class, [
                'choices' => [
                    '0-2 ans' => '0-2',
                    '3-5 ans' => '3-5',
                    '6-9 ans' => '6-9',
                    '+10 ans' => '+10'
                ]
            ])
            ->add('stock', NumberType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer une quantite de produit'
                    ]),
                    new Length([
                        'min' => 1
                    ])
                ]
            ])
            ->add('promotion', NumberType::class)
            ->add('state', ChoiceType::class, [
                'label' => 'État',
                'choices' => [
                    'bon état' => 'bon état',
                    'état correct' => 'état correct'
                ]
            ])
            ->add('length', TextType::class, [
                'label' => 'Longueur'
            ])
            ->add('width', TextType::class, [
                'label' => 'Largeur'
            ])
            ->add('heigh', TextType::class, [
                'label' => 'Hauteur'
            ])
            ->add('id_category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Catégorie'
            ])
            ->add('id_supplier', EntityType::class, [
                'class' => Supplier::class,
                'choice_label' => 'company_name',
                'label' => 'Partenaire'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
