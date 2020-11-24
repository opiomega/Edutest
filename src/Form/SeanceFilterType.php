<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Lexik\Bundle\FormFilterBundle\Filter\Form\Type as Filters;


class SeanceFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('beginTime', Filters\TextFilterType::class)
            ->add('endTime', Filters\TextFilterType::class)
            ->add('day', Filters\EntityFilterType::class, array(
                'class' => 'App\Entity\Days',
                'choice_label' => 'name',
            ))
            ->add('classe', Filters\EntityFilterType::class, array(
                'class' => 'App\Entity\Classes',
                'choice_label' => 'name',
            ))
            ->add('teacher', Filters\EntityFilterType::class, array(
                'class' => 'App\Entity\Teacher',
                'choice_label' => 'firstname',
            ))
             
        ;
        $builder->setMethod("GET");


    }

    public function getBlockPrefix()
    {
        return null;
    }
    
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'allow_extra_fields' => true,
            'csrf_protection' => false,
            'validation_groups' => array('filtering') // avoid NotBlank() constraint-related message
        ));
    }
}
