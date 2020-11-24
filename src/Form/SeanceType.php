<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
class SeanceType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            
        ->add('beginTime', TextType::class, ['attr' => ['class' => "form-control "]])
        ->add('endTime', TextType::class, ['attr' => ['class' => "form-control "]])
        
        ->add('day', EntityType::class, array(
            'class' => 'App\Entity\Days',
            'choice_label' => 'name',

            'attr'=>['class'=>'selectpicker','data-live-search'=>true],
            
           
       
           // used to render a select box, check boxes or radios
            'multiple' => true,
           // 'expanded' => true,
      
            'empty_data' => null,
            'required' => true
        ))
      /*  ->add('teacher', EntityType::class, array(
            'class' => 'App\Entity\Teacher',
            'choice_label' => 'lastname',
            'placeholder' => 'Choose Teacher',
            'empty_data' => null,
            'required' => true
        ))
        ->add('category', EntityType::class, array(
            'class' => 'App\Entity\Category',
            'choice_label' => 'name',
            'placeholder' => 'Choose Category',
            'empty_data' => null,
            'required' => true
        ))*/
        ->add('educationgroup', EntityType::class, array(
            'class' => 'App\Entity\EducationGroup',
            'choice_label' => 'name',
            'placeholder' => 'Choose group',
            'empty_data' => null,
            'required' => true,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('eg')
                ->where($er->createQueryBuilder('eg')->expr()->isNotNull('eg.online'));
            },
        ))
    ;
}
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\Seance'
        ));
    }
}
