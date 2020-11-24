<?php

namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('Content',TextareaType::class,['attr' => ['placeholder' => "Content",'class' => "form-control"]])
            ->add('choises', CollectionType::class,[
    // each entry in the array will be an "email" field
    'label'=>'Add choice',
    'entry_type' => TextType::class,
    // these options are passed to each "email" type
    'entry_options' => [
        'attr' => ['class' => 'form-control quetionChoises-box '],
    ],'allow_add' => true,'attr'=>['class'=>'MCQquestion']])
            ->add('correctChoise',null, ['attr' => ['placeholder' => "Correct choice",'class' => "form-control MCQquestion"]])
            ->add('score',null, ['attr' => ['placeholder' => "Score",'class' => "form-control"]])
            //->add('answer',null,['attr' => ['placeholder' => "Answer",'class' => "form-control QAQuestion"]])
            ->add('correctAnswer',null,['attr' => ['placeholder' => "Correct answer",'class' => "form-control QAQuestion","style"=>"display:none"]])
            ->add('type',ChoiceType::class,['choices' => [
                'MCQ'=>"MCQ",
                "Question & answer"=>'QA',
            ],'attr'=>['class'=>'form-control questionType']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
