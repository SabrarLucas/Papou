<?php

namespace App\Form;

use App\Entity\Category;
use Doctrine\ORM\QueryBuilder;
use App\Repository\CategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class SearchType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('categories', EntityType::class,[
            'label' => false,
            'required' => false,
            'class' => Category::class,
            'choice_label' => 'name',
            'query_builder' => function (CategoryRepository $cr): QueryBuilder {
                return $cr->createQueryBuilder('c')
                    ->join('c.category', 'm')
                    ->groupBy('c.id');
            },
            'expanded' => true,
            'multiple' => true
        ])
        ->add('age', ChoiceType::class, [
            'label' => false,
            'choices' => [
                '0-1 ans' => '0-1',
                '2-4 ans' => '2-4',
                '5-7 ans' => '5-7',
                '8-9 ans' => '8-9',
                '10 ans et +' => '10+'
            ],
            'expanded' => true,
            'multiple' => true
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'allow_extra_fields' => true,
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}