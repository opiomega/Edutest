<?php

namespace App\Form;

use App\Entity\University;
use App\Entity\Admission;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
class UniversityType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ['attr' => ['placeholder' => "Title" ,'class' => "form-control"]])
            ->add('studentsnumber', TextType::class, ['attr' => ['placeholder' => "Students number" ,'class' => "form-control"]])
            ->add('rank', TextType::class, ['attr' => ['placeholder' => "Rank" ,'class' => "form-control"]])
            ->add('description', TextType::class, ['attr' => ['placeholder' => "Description" ,'class' => "form-control"]])
            ->add('acceptance', TextType::class, ['attr' => ['placeholder' => "Acceptance Rate" ,'class' => "form-control"]])
            ->add('Satrange', TextType::class, ['attr' => ['placeholder' => "SAT Range" ,'class' => "form-control"]])
            ->add('deadline', TextType::class, ['attr' => ['placeholder' => "Application deadline" ,'class' => "form-control"]])
            ->add('earning', TextType::class, ['attr' => ['placeholder' => "Earning after 2 years" ,'class' => "form-control"]])
            ->add('employed', TextType::class, ['attr' => ['placeholder' => "employed after 2 years" ,'class' => "form-control"]])
            ->add('graduation', TextType::class, ['attr' => ['placeholder' => "Graduation rate" ,'class' => "form-control"]])
            ->add('photoFile',FileType::class,["required"=>false  ,'attr' => ['class' => "form-control"]])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => University::class,
            
        ]);
    }
}
