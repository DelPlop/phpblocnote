<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class CategoryFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'categories.name',
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'categories.errors.not_blank'
                    ]),
                    new Length([
                        'min' => 1,
                        'minMessage' => 'categories.errors.length_min',
                        'max' => 25,
                        'maxMessage' => 'categories.errors.length_max'
                    ])
                ]
            ])
            ->add('save', SubmitType::class, [
                'label' => $options['submitLabel']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
            'submitLabel' => 'string',
        ]);
    }
}
