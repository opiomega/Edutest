<?php

namespace App\Form;

use App\Entity\Universities;
use App\Entity\Requirement;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Intl\Intl;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class UniversitiesType extends AbstractType
{
   

    public function buildForm(FormBuilderInterface $builder, array $options)
    
    {$countries = Intl::getRegionBundle()->getCountryNames();
        $builder
            ->add('name')
            ->add('description')
            ->add('country', ChoiceType::class, array(
                'choices' => array_flip($countries),))
            ->add('state')
            ->add('type',ChoiceType::class,['choices' => [
                'Public'=>"Public",
                'Private'=>"Private",
            ]])
            ->add('website')
            ->add('financialaid')
            ->add('admissiondead')
            ->add('adresse')
            ->add('photoFile',FileType::class,["required"=>false])
            #->add('requirement',EntityType::class , [
                // looks for choices from this entity
               # 'class' => Requirement::class,
            
                // uses the User.username property as the visible option string
                #'choice_label' => 'name',])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Universities::class,
        ]);
    }
}
