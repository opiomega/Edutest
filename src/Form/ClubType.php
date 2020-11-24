<?php

namespace App\Form;

use App\Entity\Club;
use App\Entity\Student;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\DateType;
class ClubType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $required = true;
        if ($options["edit"] )
            $required = false;
        if(!$options["about"])
        $builder
            ->add('name')
            ->add('meatingDate',DateType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'required' => false,
                'placeholder' => 'Select a value',
                'attr' => array('class' => 'form-control input-inline datetimepicker',
                                 'data-provide' => 'datetimepicker',
                                 'data-format' => 'yyyy-mm-dd ', 
                                    ),
            ))
            ->add('logoFile',FileType::class,['attr' => ['class' => "form-control" ],'required'=>$required])
            ->add('students',EntityType::class, [
                // looks for choices from this entity
                'class' => Student::class,
                
                 'attr'=>['class'=>'selectpicker','data-live-search'=>true],
                 
                // uses the User.username property as the visible option string
                'choice_label' =>  function (Student $student) {
                    return $student->getFirstname() . ' ' . $student->getLastname();
                },
            
                // used to render a select box, check boxes or radios
                 'multiple' => true,
                // 'expanded' => true,
            ])
                    
            ->add('activity');
        ;
        if ($options["status"]=="Admin"){
            $builder->add('head',EntityType::class,array('class'=>'App:Teacher','choice_label' => 'firstName','attr' => ['class' => "form-control text-red" ]));
        }
        if( $options["about"]){
            $builder
              ->add('meatingDate',['required'=>false])
              ->add('about');
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Club::class,
            'edit' => array(),
            'about' => array(),
            'status' => array()
        ]);
    }
}
