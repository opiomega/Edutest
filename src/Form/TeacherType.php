<?php

namespace App\Form;

use App\Entity\City;
use App\Entity\Tests;
use App\Entity\Courses;
use App\Entity\Category;
use App\Entity\Zipcode;
use App\Entity\Teacher;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
class TeacherType extends AbstractType
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
            ->add('university', TextType::class, ['attr' => ['placeholder' => "Univeristy" ,'class' => "form-control"]])
            ->add('score', TextType::class, ['attr' => ['placeholder' => "Score" ,'class' => "form-control"]])
                ->add('testsCategory', EntityType::class, [
                'class' => Category::class,
        
            // uses the User.username property as the visible option string
            'choice_label' => 'name',
                'attr' => ['placeholder' => "Choose..." ,'class' => "form-control text-red"]])
            /*->add('courses', EntityType::class, [
                'class' => Courses::class,
        
            // uses the User.username property as the visible option string
            'choice_label' => 'name',
                'attr' => ['placeholder' => "Choose..." ,'class' => "form-control text-red"]])
            ->add('tests', EntityType::class, [
                'class' => Tests::class,
        
            // uses the User.username property as the visible option string
            'choice_label' => 'name',
                'attr' => ['placeholder' => "Choose..." ,'class' => "form-control text-red"]])*/
                
                
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Teacher::class,
            'new' => array(),
        ]);
    }
}
