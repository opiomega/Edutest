<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Zipcode;
use App\Entity\Student;
use App\Entity\Classes;
use App\Entity\Educationlevel;
use App\Entity\Courses;
use App\Entity\Hearaboutus;
use App\Entity\EducationGroup;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Teacher;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['new']){
            $builder
                ->add("email", EmailType::class, ['attr' => ['placeholder' => "Email" ,'class' => "form-control"],'property_path' => 'user.email'])
                ->add("phone", TextType::class, ['attr' => ['placeholder' => "Phone" ,'class' => "form-control"],'property_path' => 'user.phone'])
                ->add("adress", TextType::class, ['attr' => ['placeholder' => "Adress" ,'class' => "form-control"],'property_path' => 'user.adress'])
                ->add("zipcode", TextType::class,['attr' => ['placeholder' => "Zipcode" ,'class' => "form-control text-red"],'property_path' => 'user.zipcode'])
                ->add("city", EntityType::class,['attr' => ['placeholder' => "City" ,'class' => "form-control text-red"],'class'=> City::class,'choice_label' => 'name','property_path' => 'user.city'])
                
            ;
        }
        $builder
            
            ->add('firstname', TextType::class, ['attr' => ['placeholder' => "First name" ,'class' => "form-control"]])
            ->add('lastname', TextType::class, ['attr' => ['placeholder' => "Last name" ,'class' => "form-control"]])
            ->add('school', TextType::class, ['attr' => ['placeholder' => "School" ,'class' => "form-control"]])
            ->add('firstparentname', TextType::class, ['attr' => ['placeholder' => "father's name" ,'class' => "form-control"]])
            ->add('firstparentjob', TextType::class, ['attr' => ['placeholder' => "Father's job" ,'class' => "form-control"]])
            ->add('secondparentname', TextType::class, ['attr' => ['placeholder' => "Mother's name" ,'class' => "form-control"]])
            ->add('secondparentjob', TextType::class, ['attr' => ['placeholder' => "Mother's job" ,'class' => "form-control"]])
            ->add('fristparentemail', EmailType::class,['attr' => ['placeholder' => "Email" ,'class' => "form-control"]])
            ->add('firstparentnumber', TextType::class, ['attr' => ['placeholder' => "Father's number" ,'class' => "form-control"]])
            ->add('secondparentemail', EmailType::class,['attr' => ['placeholder' => "Email" ,'class' => "form-control"]])
            ->add('secondparentnumbre', TextType::class, ['attr' => ['placeholder' => "mother's number" ,'class' => "form-control"]])
            ->add('comments', TextType::class, ['attr' => ['placeholder' => "Comment" ,'class' => "form-control"]])
            ->add('membretalk', TextType::class, ['attr' => ['placeholder' => "membre talk" ,'class' => "form-control"]])
          //  ->add('classes', EntityType::class, [
              //  'class' => Classes::class,
        
            // uses the User.username property as the visible option string
         //   'choice_label' => 'name',
          //      'attr' => ['placeholder' => "Choose..." ,'class' => "form-control text-red"]])
            ->add('courses', EntityType::class, [
                'class' => Courses::class,
        
            // uses the User.username property as the visible option string
            'choice_label' => 'name',
                'attr' => ['placeholder' => "Choose..." ,'class' => "form-control text-red"]])
            ->add('educationlevel', EntityType::class, [
                'class' => Educationlevel::class,
        
            // uses the User.username property as the visible option string
            'choice_label' => 'name',
                'attr' => ['placeholder' => "Choose..." ,'class' => "form-control text-red"]])
            ->add('hearaboutus', EntityType::class, [
                'class' => Hearaboutus::class,
        
            // uses the User.username property as the visible option string 
            'choice_label' => 'name',
                'attr' => ['placeholder' => "Choose..." ,'class' => "form-control"]])
            ->add("specialskills", TextType::class, ['attr' => ['placeholder' => "Your skills" ,'class' => "form-control"]])
            ->add("achievement", TextType::class, ['attr' => ['placeholder' => "Your achievment" ,'class' => "form-control"]])
            ->add("hobbies", TextType::class, ['attr' => ['placeholder' => "Your hobbies" ,'class' => "form-control"]])
            ->add('online',ChoiceType::class,['choices' => [
                'Online'=>1,
                'Offline'=>0,
            ]])
        ;
        if (in_array('ROLE_SUPER_ADMIN',$options['roles'])){
            $builder->add('teacher', EntityType::class, [
                // looks for choices from this entity
                'class' => Teacher::class,
            
                // uses the User.username property as the visible option string
                'choice_label' => 'lastname',
            
                'attr' => ['placeholder' => "Choose..." ,'class' => "form-control text-red"]
            ])->add('levelTestType', EntityType::class, [
                        //'class' => Hearaboutus::class,
                        'class' => Category::class,
                        // uses the User.username property as the visible option string
                        'choice_label' => 'name',
                        'attr' => ['placeholder' => "Choose..." ,'class' => "form-control text-red"]])
            /*->add('eductionGroup', EntityType::class, [
                         //'class' => Hearaboutus::class,
        
                        'class' => EducationGroup::class,
                         // uses the User.username property as the visible option string
                         'choice_label' => 'name',
                         'attr' => ['placeholder' => "Choose..." ,'class' => "form-control text-red"]])*/;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
            'roles' => array(),
            'new' => array()
        ]);
    }
}

