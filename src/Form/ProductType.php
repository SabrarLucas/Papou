<?php

namespace App\Form;

use App\Entity\Product;
use App\Entity\Category;
use Doctrine\ORM\QueryBuilder;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

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
                    '0-1 ans' => '0-1',
                    '2-4 ans' => '2-4',
                    '5-7 ans' => '5-7',
                    '8-9 ans' => '8-9',
                    '10 ans et +' => '10+'
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
                'label' => 'Categorie',
                'query_builder' => function (CategoryRepository $cr): QueryBuilder {
                    return $cr->createQueryBuilder('c')
                        ->join('c.category', 'm')
                        ->groupBy('c.id');
                },
            ])
            ->add('image0', FileType::class, [
                'multiple' => true,
                'mapped' =>false,
                'required' => false,
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
