<?php

namespace App\Form;

use App\Entity\Module;
use App\Entity\EducationGroup;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ModuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options )
    {
        $required = true;
        if ($options["edit"])
            $required = false;
        if ($options["selectOptions"] != null){
        foreach ($options["selectOptions"] as $option){
        $builder
            ->add($option,TextType::class,array('attr' => ['class' => "form-control" ]));
         if ($option == 'typeCourse'){
            $builder->add('typeCourse',EntityType::class,array('class'=>'App:TypeCourse','choice_label' => 'name','attr' => ['class' => "form-control" ]));
         }
         if ($option == 'category'){
            $builder->add('category',EntityType::class,array('class'=>'App:Category','choice_label' => 'name','attr' => ['class' => "form-control" ]))
                    ->add('classe',EntityType::class,['class'=>'App:Classes','choice_label' => 'name']);;
         }
         if ($option == 'supportPdfFile'){
             $builder->add('supportPdfFile',FileType::class,['attr' => ['class' => "form-control" ] ,'required'=>$required]);
         }
         if ($option == 'video'){
            $builder->add('video',null, ['label' => 'Video www.youtube.com/watch?v=','attr' => ['class' => "form-control" ]]);
        }
        if ($option == 'content'){
            $builder->add('content', TextareaType::class, array(
                'attr' => array(
                    'class' => 'tinymce text-dark',
                    'data-theme' => 'bbcode' // Skip it if you want to use default theme
                )
            ));
          }
          /*if ($option == 'seance'){
            $builder->add('seance',EntityType::class,array('class'=>'App:Seance','choice_label' => 'informations','attr' => ['class' => "form-control" ]));
          }*/
        }
    }
        $builder->add('educationGroup',EntityType::class,['class' => EducationGroup::class,'choice_label' => 'name','attr' => ['class' => "form-control text-red" ]]);
        
       
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Module::class,
            'selectOptions' => array(),
            'edit'=>array()
        ]);
    }
}
