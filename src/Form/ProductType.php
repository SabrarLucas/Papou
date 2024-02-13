<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
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
                        'minMessage' => 'le nom du produit est trop court',
                        'max' =>  100,
                        'maxMessage' => 'le nom du produit est trop long',
                    ])
                ]
            ])
            ->add('description', TextareaType::class, [
                'attr' => [
                    'rows' => 5
                ],
                'required' => false
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer un prix au produit'
                    ]),
                    new Length([
                        'max' => 9,
                        'maxMessage' => 'le jouer est trop cher',
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
                        'min' => 1,
                    ])
                ]
            ])
            ->add('promotion', NumberType::class, [
                'required' => false
            ])
            ->add('state', ChoiceType::class, [
                'label' => 'État',
                'choices' => [
                    'bon etat' => 'bon etat',
                    'etat correct' => 'etat correct'
                ]
            ])
            ->add('length', TextType::class, [
                'label' => 'Longueur',
                'required' => false
            ])
            ->add('width', TextType::class, [
                'label' => 'Largeur',
                'required' => false
            ])
            ->add('heigh', TextType::class, [
                'label' => 'Hauteur',
                'required' => false
            ])
            ->add('id_category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Categorie'
            ])
            ->add('image0', FileType::class, [
                'multiple' => true,
                'mapped' =>false,
                'label' => 'Image'
            ])
            ->add('image1', FileType::class, [
                'multiple' => true,
                'mapped' =>false,
                'required' => false,
                'label' => false
            ])
            ->add('image2', FileType::class, [
                'multiple' => true,
                'mapped' =>false,
                'required' => false,
                'label' => false
            ])
            ->add('image3', FileType::class, [
                'multiple' => true,
                'mapped' =>false,
                'required' => false,
                'label' => false
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
