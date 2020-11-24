<?php
namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\FileType;

class PhotoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add('photoFile',FileType::class,["required"=>false/*,'attr'=>['class'=>'text-white','style'=>'padding-left: 30px;
    padding-top: 13px;
    font-size:14px;
    background: none;
    border:none;']*/]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => User::class,
            'edit' => array(),
            'roles' => array(),
        ));
    }
}