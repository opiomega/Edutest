<?php

namespace App\Form;

use App\Entity\EducationGroup;
use App\Entity\Student;
use App\Entity\Teacher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class EducationGroupType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            
            /*->add('students',EntityType::class, [
    // looks for choices from this entity
    'class' => Student::class,
    
    // uses the User.username property as the visible option string
    'choice_label' => 'firstAndLstname',

    // used to render a select box, check boxes or radios
     'multiple' => true,
    // 'expanded' => true,
     'attr'=>['name'=>"students"]
])*/
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
            ->add('classe',EntityType::class, [
                'label' => 'Courses',
    // looks for choices from this entity
    'class' => 'App:Classes'])
        ;
    if (!$options['online']){
        $builder->add('teacher',EntityType::class, [
        // looks for choices from this entity
        'class' => Teacher::class,
        'attr' => ['class' => "text-red teacherChoice"/*,"style"=>"display:none;"*/],
        'label_attr' => ['class' => "teacherChoice","style"=>"display:none;"],
        // uses the User.username property as the visible option string
        'choice_label' => 'firstAndLstname']);
    }
    else {
        $builder->add('teacher',EntityType::class, [
        // looks for choices from this entity
        'placeholder' => 'Choose a teacher',
        'required' => false,
        'class' => Teacher::class,
        'attr' => ['class' => "text-red teacherChoice"/*,"style"=>"display:none;"*/],
        'label_attr' => ['class' => "teacherChoice","style"=>"display:none;"],
        // uses the User.username property as the visible option string
        'choice_label' => 'firstAndLstname']);
    }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => EducationGroup::class,
            'online' => array(),
        ]);
    }
}
