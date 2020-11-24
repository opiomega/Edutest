<?php

namespace App\Form;

use App\Entity\Test;
use App\Entity\EducationGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\File;

class TestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /*$required = true;
        if ($options["edit"])*/
            $required = false;
        $status = 'Teacher';
        if ($options["admin"])
            $status = 'admin';
        foreach ($options["selectOptions"] as $option){
            $builder->add($option,TextType::class,array('attr' => ['class' => "form-control" ]));
            if ($option == 'category'){
                $builder->add('category',EntityType::class,array('class'=>'App:Category','choice_label' => 'name','attr' => ['class' => "form-control text-red" ]));
                        
            }
            if ($option == 'classe'){
                $builder->add('classe',EntityType::class,['class'=>'App:Classes','choice_label' => 'name']);
            }
            if ($option == 'supportPdfFile'){
             $builder->add('supportPdfFile',FileType::class,['attr' => ['class' => "form-control" ],'required' => $required,'constraints' => [
            new File([
            'maxSize' => '1G','maxSizeMessage'=>'The uploaded file was too large. Please try to upload a smaller file.'])]]);
            }
            /*if ($option == 'type'){
                if ($status == 'admin')
                $builder->add('type',ChoiceType::class, [
                    'choices'  => [
                        'level test' => 'level',
                        'normal test' => 'normal',
                        
                    ], 'attr' => ['class' => "form-control text-red"]
                ]);
                
            }*/
            if ($option == 'educationGroup'){
                $builder->add('educationGroup',EntityType::class,['class' => EducationGroup::class,'choice_label' => 'name','attr' => ['class' => "form-control text-red educationGroup" ],'label_attr' => ['class' => "educationGroup" ]]);
            }
            if ($option == 'deadline')
                $builder->add('deadline', null, [
                'placeholder' => [
                    'year' => 'Year', 'month' => 'Month', 'day' => 'Day',
                ],
                'widget' => 'choice',
                'format' => 'yyyy-MM-dd',
                'years' => range(date('Y'), 2035),
                ]);
            if ($option=='listeningFile')
                 $builder->add('listeningFile',FileType::class,['label'=>'Listening audio upload','attr' => ['class' => "form-control" ],'required' => $required,'constraints' => [
                new File([
                    'maxSize' => '1G','maxSizeMessage'=>'The uploaded file was too large. Please try to upload a smaller file.','mimeTypes' => [
                'audio/mpeg',
                    ],
                'mimeTypesMessage' => 'Please upload a valid audio document']),
                ]]);
                
        }
        
        /*$builder
            ->add('title')
            ->add('description')
            ->add('supportPdfFile',FileType::class)
        ;*/
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Test::class,
            'selectOptions' => array(),
            'edit' => array(),
            'admin' => array(),
        ]);
    }
}
