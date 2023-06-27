<?php

namespace App\Form;

use App\Entity\Review;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;

class ReviewFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('comment', TextType::class, [
                'attr' => [
                    'class' => 'review-input',
                    'placeholder' => 'Add a review...',
                    'autocomplete' => 'off'
                ],
                'label' => false,
                'required' => false
            ])
            ->add('rating', RangeType::class, [
                'attr' => [
                    'class' => 'review-range',
                    'min' => 1,
                    'max' => 5,
                ],
                'label' => 'Rating'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Review::class,
        ]);
    }
}
