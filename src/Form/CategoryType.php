<?php

namespace App\Form;

use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class CategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['attr' => ['placeholder' => "Category name" ,'class' => "form-control text-dark"]])
            ->add('description', TextType::class, ['attr' => ['placeholder' => "Description.." ,'class' => "form-control text-dark"]])
            /*->add('classes',EntityType::class,['label'=>'Add classes','class'=>'App:Classes',
                'multiple'  => true,'choice_label' => 'name'])*/
            ->add('photoFile',FileType::class,['attr' => ['class' => "form-control" ],'required'=>true])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class,
        ]);
    }
}
