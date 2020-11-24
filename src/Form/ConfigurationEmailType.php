<?php

namespace App\Form;

use App\Entity\ConfigurationEmail;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ConfigurationEmailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subject',ChoiceType::class,['label'=>'Subject','choices' => [
                'deadlines'=>'deadlines','absence'=>'absence','Placement test'=>'levelTest','Counseling'=>'counseling',
            ]])
            ->add('content',TextareaType::class,[
                'attr'=>[
                    'style'=>'min-height:250px;',
                    'class' => 'tinymce text-dark',
                    'data-theme' => 'bbcode' // Skip it if you want to use default theme
                ]
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ConfigurationEmail::class,
        ]);
    }
}
