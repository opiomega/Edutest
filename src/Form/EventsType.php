<?php

namespace App\Form;

use App\Entity\Events;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
class EventsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('name', TextType::class, ['attr' => ['placeholder' => "Event name" ,'class' => " "]])
        ->add('description', TextareaType::class, ['attr' => ['placeholder' => "Description" ,'class' => ""]])
        ->add('date', TextType::class, ['attr' => ['placeholder' => "Date" ,'class' => "form-control datepicker "]])
        ->add('photoFile',FileType::class, ['attr' => ['class' => "form-control"],"required"=>false])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Events::class,
        ]);
    }
}
