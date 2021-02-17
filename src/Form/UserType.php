<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\DBAL\Types\IntegerType;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RadioType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\City;
use App\Entity\Zipcode;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;



class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class)
            ->add('phone', TextType::class)
            ->add('adress', TextType::class)
            ->add('datebirth', DateType::class ,array(  'widget' => 'single_text'))
            ->add('citybirth', TextType::class)
            ->add('sexe',ChoiceType::class,array(
                'choices'  => array(
                    'Female' => 'F',
                    'Male' => 'M',

                ),'expanded'=>true

            ))

            ->add('zipcode', TextType::class)







            ->add('Status', ChoiceType::class,

                array(
                    'choices'  => array(
                        'Student' => 'Student',
                        'Teacher' => 'Teacher',

                    ),'expanded'=>true)
            );








        if (!$options["edit"]) {
            $builder
                ->add('Password', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'first_options'  => array('label' => 'Password'),
                    'second_options' => array('label' => 'Repeat Password'),
                    'constraints' => [
                        new Length([
                            'min' => 8,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                        new Regex([
                            "pattern" => "/^(?=.*[!@#$%^&*])/m",
                            "message" => 'Your password must contain a special character like (!,@,#,$,%,^,&)',
                        ]),
                    ]
                ))
                //->add('photoFile',FileType::class,['attr' => ['class' => "form-control" ],"required"=>true])
            ;
        } else {
            $builder->add('photoFile', FileType::class, ['attr' => ['class' => "form-control"], "required" => false]);
        }
        if (in_array('ROLE_SUPER_ADMIN', $options['roles'])) {
            $builder->add('Active', ChoiceType::class, [
                'choices'  => [
                    'Oui' => '1',
                    'Non' => '0',

                ]
            ]);
        }
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
