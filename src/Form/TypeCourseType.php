<?php

namespace App\Form;

use App\Entity\TypeCourse;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class TypeCourseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Name', TextType::class,['attr' => ['placeholder' => "Title" ,'class' => "form-control"]])
            ->add('fields_available', CollectionType::class,[
    // each entry in the array will be an "email" field
    'entry_type' => TextType::class,
    // these options are passed to each "email" type
    'entry_options' => [
        'attr' => ['class' => 'form-control fieldAvailabl-box '],
    ],'allow_add' => true,])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TypeCourse::class,
        ]);
    }
}
