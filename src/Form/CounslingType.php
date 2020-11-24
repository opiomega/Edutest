<?php

namespace App\Form;

use App\Entity\Counsling;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CounslingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('begintime', TextType::class, ['label'=>'Start time','attr' => ['class' => "form-control "]])
            ->add('endtime', TextType::class, ['label'=>'End time','attr' => ['class' => "form-control "]])
           /* ->add('day', EntityType::class, array(
                'class' => 'App\Entity\Days',
                'choice_label' => 'name',
                'placeholder' => 'Select a day',
                'empty_data' => null,
                'required' => true
            ))*/
          /*  ->add('educationgroup', EntityType::class, array(
                'class' => 'App\Entity\EducationGroup',
                'choice_label' => 'name',
                'placeholder' => 'Choose group',
                'empty_data' => null,
                'required' => true
            ))*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Counsling::class,
        ]);
    }
}
