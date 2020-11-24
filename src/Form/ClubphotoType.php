<?php

namespace App\Form;

use App\Entity\Clubphoto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\Extension\Core\Type\TextType;
class ClubphotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('photocFile', FileType::class, [
            
            'attr'     => [
                'class'=>'form-2-box form-control',
          
            
            ]
           
        ])
        ->add('eventname',TextType::class,['attr'=>['class'=>'form-control']])
    ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
    $resolver->setDefaults([
        'data_class' =>Clubphoto::class,
    ]);
    }
}