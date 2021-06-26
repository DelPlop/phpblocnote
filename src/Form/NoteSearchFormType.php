<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class NoteSearchFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('search', TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'class' => 'normal-form-input',
                    'placeholder' => 'notes.search_placeholder',
                    'id' => 'recherche-note-haut'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'note_search.errors.not_blank'
                    ])
                ]
            ])
            ->add('valid_search', SubmitType::class, [
                'label' => 'OK',
                'attr' => [
                    'class' => 'search-ok no-space'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
