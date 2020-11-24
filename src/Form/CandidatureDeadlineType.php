<?php

namespace App\Form;

use App\Entity\CandidatureDeadline;

use Doctrine\DBAL\Types\TextType as TypesTextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
//use Symfony\Component\Form\Extension\Core\Type\DateType;

class CandidatureDeadlineType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('test' ,DateTimeType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'required' => false,
                'placeholder' => 'Select a value',
                'attr' => array('class' => 'form-control input-inline datetimepicker',
                                 'data-provide' => 'datetimepicker',
                                 'data-format' => 'yyyy-mm-dd HH:ii A', 
                                 
                                    ),
            ))
            ->add('letterOfRecommendation',DateTimeType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'required' => false,
                'placeholder' => 'Select a value',
                'attr' => array('class' => 'form-control input-inline datetimepicker',
                                 'data-provide' => 'datetimepicker',
                                 'data-format' => 'yyyy-mm-dd HH:ii A', 
                                 
                                    ),
            ))
            ->add('transcriptBac',DateTimeType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'required' => false,
                'placeholder' => 'Select a value',
                'attr' => array('class' => 'form-control input-inline datetimepicker',
                                 'data-provide' => 'datetimepicker',
                                 'data-format' => 'yyyy-mm-dd HH:ii A', 
                                 
                                    ),
            ))
            ->add('passport',DateTimeType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'required' => false,
                'placeholder' => 'Select a value',
                'attr' => array('class' => 'form-control input-inline datetimepicker',
                                 'data-provide' => 'datetimepicker',
                                 'data-format' => 'yyyy-mm-dd HH:ii A', 
                                 
                                    ),
            ))
            ->add('cin',DateTimeType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'required' => false,
                'placeholder' => 'Select a value',
                'attr' => array('class' => 'form-control input-inline datetimepicker',
                                 'data-provide' => 'datetimepicker',
                                 'data-format' => 'yyyy-mm-dd HH:ii A', 
                                 
                                    ),
            ))
            ->add('letterOfRecommendationMath',DateTimeType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'required' => false,
                'placeholder' => 'Select a value',
                'attr' => array('class' => 'form-control input-inline datetimepicker',
                                 'data-provide' => 'datetimepicker',
                                 'data-format' => 'yyyy-mm-dd HH:ii A', 
                                 
                                    ),
            ))
            ->add('transcriptThird',DateTimeType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'required' => false,
                'placeholder' => 'Select a value',
                'attr' => array('class' => 'form-control input-inline datetimepicker',
                                 'data-provide' => 'datetimepicker',
                                 'data-format' => 'yyyy-mm-dd HH:ii A', 
                                 
                                    ),
            ))
            ->add('transcriptSecond',DateTimeType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'required' => false,
                'placeholder' => 'Select a value',
                'attr' => array('class' => 'form-control input-inline datetimepicker',
                                 'data-provide' => 'datetimepicker',
                                 'data-format' => 'yyyy-mm-dd HH:ii A', 
                                 
                                    ),
            ))
            ->add('transcriptFirst',DateTimeType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'required' => false,
                'placeholder' => 'Select a value',
                'attr' => array('class' => 'form-control input-inline datetimepicker',
                                 'data-provide' => 'datetimepicker',
                                 'data-format' => 'yyyy-mm-dd HH:ii A', 
                                 
                                    ),
            ))
            ->add('survey',DateTimeType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'required' => false,
                'placeholder' => 'Select a value',
                'attr' => array('class' => 'form-control input-inline datetimepicker',
                                 'data-provide' => 'datetimepicker',
                                 'data-format' => 'yyyy-mm-dd HH:ii A', 
                                 
                                    ),
            ))
            ->add('bankStatement',DateTimeType::class, array(
                'widget' => 'single_text',
                'html5' => false,
                'required' => false,
                'placeholder' => 'Select a value',
                'attr' => array('class' => 'form-control input-inline datetimepicker',
                                 'data-provide' => 'datetimepicker',
                                 'data-format' => 'yyyy-mm-dd HH:ii A', 
                                 
                                    ),
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CandidatureDeadline::class,
        ]);
    }
}
