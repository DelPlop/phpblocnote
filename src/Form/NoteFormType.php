<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Note;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Validator\Constraints\Count;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class NoteFormType extends AbstractType
{
    /**
     * @var Security
     */
    private $security;


    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $label = $options['submitLabel'];
        $category = $options['category'];
        $choices = [
            'private' => 'notes.visibility.private',
            'shared' => 'notes.visibility.shared',
            'public' => 'notes.visibility.public'
        ];

        $builder
            ->add('title', TextType::class, [
                'label' => 'notes.title_required',
                'required' => false,
                'constraints' => [
                    new NotBlank([
                        'message' => 'notes.errors.title.not_blank'
                    ]),
                    new Length([
                        'min' => 1,
                        'minMessage' => 'notes.errors.title.length_min',
                        'max' => 255,
                        'maxMessage' => 'notes.errors.title.length_max'
                    ])
                ]
            ])
            ->add('categories', EntityType::class,
                [
                    'label' => 'categories.category_required',
                    'class' => Category::class,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('c')
                            ->andWhere('c.user = :user')
                            ->setParameter('user', $this->security->getUser())
                            ->orderBy('c.position', 'ASC');
                    },
                    'multiple' => true,
                    'expanded' => true,
                    'choice_attr' => function ($val, $key, $index) use ($category) {
                        if (!empty($category) && $index == $category->getId()) {
                            $attrs['checked'] = true;
                        }
                        else {
                            $attrs['checked'] = false;
                        }

                        return $attrs;
                    },
                    'required' => false,
                    'constraints' => [
                        new Count([
                            'min' => 1,
                            'minMessage' => 'notes.errors.category.length_min'
                        ])
                    ]
                ])
            ->add('content', TextareaType::class, [
                'label' => 'notes.desc',
                'row_attr' => ['class' => 'textarea'],
                'required' => false
            ])
            ->add('visibility', ChoiceType::class, [
                'label' => 'notes.visible_by_required',
                'choices' => \array_flip($choices),
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'notes.errors.visibility.not_blank'
                    ])
                ]
            ])
            ->add('save', SubmitType::class, ['label' => $label])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Note::class,
            'submitLabel' => 'string',
            'category' => Category::class
        ]);
    }
}
