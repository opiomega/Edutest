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
use PhpParser\Parser\Multiple;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Teacher;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType ;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if (!$options['new']){
            $builder
                ->add("email", EmailType::class, ['property_path' => 'user.email'])
                ->add("phone", TextType::class)
                ->add("adress", TextType::class, [ 'property_path' => 'user.adress'])
                ->add("zipcode", TextType::class)
                ->add("city", EntityType::class,['class'=> City::class,'choice_label' => 'name','property_path' => 'user.city'])

            ;
        }
        $builder

            ->add('firstname', TextType::class )

            ->add('lastname', TextType::class)
            ->add('school', TextType::class)


            ->add('travelabroad',ChoiceType::class,array(
                'choices'  => array(
                    'Yes' => 'Y',
                    'No' => 'N',

                ),'expanded'=>true))





            ->add('parentspayeducation',ChoiceType::class,array(
                'choices'  => array(
                    'Yes'=>'yes',
                    'No'=>'no',
                    'Maybe'=>'maybe'

                ),'expanded'=>true))

            ->add('payeducation',ChoiceType::class,array(
                'choices'  => array(
                    'Yes'=>'yes',
                    'No'=>'no',
                    'Maybe'=>'maybe'

                ),'expanded'=>true))

            ->add('preferredlanguage',ChoiceType::class,array(
                'choices'  => array(
                    'English'=>'english',
                    'Frensh'=>'frensh',


                ),'expanded'=>true))
            ->add('enrollededutest',ChoiceType::class,array(
                'choices'  => array(
                    'Yes'=>'yes',
                    'No'=>'no',


                ),'expanded'=>true))
            ->add('organisationmembership',ChoiceType::class,array(
                'choices'  => array(
                    'Yes'=>'yes',
                    'No'=>'no',


                ),'expanded'=>true))
            
                ->add('travelreason',ChoiceType::class,[
                'choices'  => [
                    'Tourism' => 'tourism',
                    'Education' => 'education',
                    'Culture' => 'culture',
                    'Job' => 'job',
                ],
                'preferred_choices' => ['muppets', 'arr'],
                ])

            ->add('schoollocation', TextType::class)
            ->add('firstparentname', TextType::class)
            ->add('firstparentjob', TextType::class)
            ->add('secondparentname', TextType::class)
            ->add('secondparentjob', TextType::class)
            ->add('fristparentemail', EmailType::class)
            ->add('firstparentnumber', TextType::class)
            ->add('secondparentemail', EmailType::class)
            ->add('secondparentnumbre', TextType::class)

            ->add('comments', TextareaType::class, ['attr' => ['placeholder' => "Tell me about yourself" ,'class' => "form-control"]])
            ->add('membretalk', TextType::class, ['attr' => ['placeholder' => "who did you speack to?" ,'class' => "form-control"]])
            //  ->add('classes', EntityType::class, [
            //  'class' => Classes::class,

            // uses the User.username property as the visible option string
            //   'choice_label' => 'name',
            //      'attr' => ['placeholder' => "Choose..." ,'class' => "form-control text-red"]])
            ->add('courses', EntityType::class, [
                'class' => Courses::class,'placeholder' => '-- Select the course would you like to take -- ',

                'choice_label' => 'name',
                'multiple' => true,'expanded'=>true,
                'attr' => ['placeholder' => "Choose..." ,'class' => "form-control text-red"]])
            ->add('educationlevel', EntityType::class, [
                'class' => Educationlevel::class,'placeholder' => '-- Select your educational level --',

                // uses the User.username property as the visible option string
                'choice_label' => 'name',
                'attr' => ['placeholder' => "Choose..." ,'class' => "form-control text-red"]])
            ->add('hearaboutus', EntityType::class, [
                'class' => Hearaboutus::class,'placeholder' => 'How did you here about us',

                // uses the User.username property as the visible option string
                'choice_label' => 'name',
                'attr' => ['placeholder' => "Choose..." ,'class' => "form-control"]])
            ->add("specialskills", TextareaType::class, ['attr' => ['placeholder' => "Your skills" ,'class' => "form-control"]])
            ->add("achievement", TextareaType::class, ['attr' => ['placeholder' => "Your achievment" ,'class' => "form-control" ]])
            ->add("hobbies", TextareaType::class, ['attr' => ['placeholder' => "Your hobbies" ,'class' => "form-control"]])
            ->add('online',ChoiceType::class,array(
        'choices'  => array(
            'Online'=>1,
            'Offline'=>0,

        ),'expanded'=>true))
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
                'class' => Category::class,'placeholder' => '-- Select your level test --',
                // uses the User.username property as the visible option string
                'choice_label' => 'name',
                'attr' => ['placeholder' => "select your level test" ,'class' => "form-control "]])
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