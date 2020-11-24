<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Profession;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class ProfessionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['attr' => ['class' => "form-control "]])
            ->add('categories',EntityType::class, [
                // looks for choices from this entity
                'class' => Category::class,
                
                 'attr'=>['class'=>'selectpicker','data-live-search'=>true],
                 
                // uses the User.username property as the visible option string
                'choice_label' =>  function (Category $category) {
                    return $category->getname();
                },
            
                // used to render a select box, check boxes or radios
                 'multiple' => true,
                // 'expanded' => true,
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Profession::class,
        ]);
    }
}
