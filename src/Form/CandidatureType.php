<?php

namespace App\Form;


use App\Entity\Candidature;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Validator\Constraints\File;
class CandidatureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // $required = ["required"=>true];
        // if ($options["edit"])
        //     $required = ["required"=>false];
        $builder
        ->add('testFile',FileType::class, ['attr' => ['class' => "form-control" ],
        'constraints' => [
            new File([
            'maxSize' => '2M',
            'mimeTypes' => [
                'application/pdf',
                'application/x-pdf',             
                    ],
                'mimeTypesMessage' => 'Please upload a valid PDF document',
                ])],"required"=>false/*,"disabled"=>$options['fieldDisabled'][0]]*/])
        ->add('letterOfRecommendationFile',FileType::class, ['attr' => ['class' => "form-control"],
        'constraints' => [
            new File([
            'maxSize' => '2M',
            'mimeTypes' => [
                'application/pdf',
                'application/x-pdf',
                
                    ],
                'mimeTypesMessage' => 'Please upload a valid PDF document',
                ])],"required"=>false/*,"disabled"=>$options['fieldDisabled'][0]*/])
        ->add('letterOfRecommendationMathFile',FileType::class, ['attr' => ['class' => "form-control"],
        'constraints' => [
            new File([
            'maxSize' => '2M',
            'mimeTypes' => [
                'application/pdf',
                'application/x-pdf',
                
                    ],
                'mimeTypesMessage' => 'Please upload a valid PDF document',
                ])],"required"=>false/*,"disabled"=>$options['fieldDisabled'][0]*/])
        ->add('transcriptBacFile',FileType::class, ['attr' => ['class' => "form-control"],'constraints' => [
            new File([
            'maxSize' => '2M',
            'mimeTypes' => [
                'application/doc',
                'application/x-doc',
                'application/zip',
                    ],
                'mimeTypesMessage' => 'Please upload a valid Word document',
                ])
                ]
        ,"required"=>false/*,"disabled"=>$options['fieldDisabled'][0]*/])
        ->add('transcriptThirdFile',FileType::class, ['attr' => ['class' => "form-control"],'constraints' => [
            new File([
            'maxSize' => '2M',
            'mimeTypes' => [
                'application/doc',
                'application/x-doc',
                'application/zip',
                    ],
                'mimeTypesMessage' => 'Please upload a valid Word document',
                ])
                ]
        ,"required"=>false/*,"disabled"=>$options['fieldDisabled'][0]*/])

        ->add('transcriptSecondFile',FileType::class, ['attr' => ['class' => "form-control"],'constraints' => [
            new File([
            'maxSize' => '2M',
            'mimeTypes' => [
                'application/doc',
                'application/x-doc',
                'application/zip',
                    ],
                'mimeTypesMessage' => 'Please upload a valid Word document',
                ])
                ]
        ,"required"=>false/*,"disabled"=>$options['fieldDisabled'][0]*/])
        ->add('transcriptFirstFile',FileType::class, ['attr' => ['class' => "form-control"],'constraints' => [
            new File([
            'maxSize' => '2M',
            'mimeTypes' => [
                'application/doc',
                'application/x-doc',
                'application/zip',
                
                    ],
                'mimeTypesMessage' => 'Please upload a valid Word document',
                ])
                ]
        ,"required"=>false/*,"disabled"=>$options['fieldDisabled'][0]*/])
        ->add('surveyFile',FileType::class, ['attr' => ['class' => "form-control"],
        'constraints' => [
            new File([
            'maxSize' => '2M',
            'mimeTypes' => [
                'application/pdf',
                'application/x-pdf',             
                    ],
                'mimeTypesMessage' => 'Please upload a valid PDF document',
                ])],
        "required"=>false/*,"disabled"=>$options['fieldDisabled'][0]*/])
        ->add('bankStatementFile',FileType::class, ['attr' => ['class' => "form-control"],'constraints' => [
            new File([
            'maxSize' => '2M',
            'mimeTypes' => [
                'application/doc',
                'application/x-doc',
                'application/zip',
                    ],
                'mimeTypesMessage' => 'Please upload a valid Word document',
                ])
                ],"required"=>false/*,"disabled"=>$options['fieldDisabled'][0]*/])
        ->add('passportFile',FileType::class, ['attr' => ['class' => "form-control"],'constraints' => [
            new File([
            'maxSize' => '2M',
            'mimeTypes' => [
                'application/doc',
                'application/x-doc',
                'application/zip',
                    ],
                'mimeTypesMessage' => 'Please upload a valid Word document',
                ])
                ],"required"=>false/*,"disabled"=>$options['fieldDisabled'][0]*/])
        ->add('passportFilepdf',FileType::class, ['mapped'=>false,'attr' => ['class' => "form-control"],'constraints' => [
            new File([
            'maxSize' => '2M',
            'mimeTypes' => [
                'application/pdf',
                'application/x-pdf',
                
                    ],
                'mimeTypesMessage' => 'Please upload a valid PDF document',
                ])
            ],"required"=>false/*,"disabled"=>$options['fieldDisabled'][0]*/])

        ->add('cinFile',FileType::class, ['attr' => ['class' => "form-control"],'constraints' => [
            new File([
            'maxSize' => '2M',
            'mimeTypes' => [
                'application/doc',
                'application/x-doc',
                'application/zip',
                    ],
                'mimeTypesMessage' => 'Please upload a valid Word document',
                ])
                ],"required"=>false/*,"disabled"=>$options['fieldDisabled'][0]*/])
        ->add('cinFilepdf',FileType::class, ['mapped'=>false,'attr' => ['class' => "form-control"],'constraints' => [
            new File([
            'maxSize' => '2M',
            'mimeTypes' => [
                'application/pdf',
                'application/x-pdf',
                
                    ],
                'mimeTypesMessage' => 'Please upload a valid PDF document',
                ])
            ],"required"=>false/*,"disabled"=>$options['fieldDisabled'][0]*/])

        ->add('username', TextType::class, ['mapped'=>false,'attr' => ['class' => "form-control"],"required"=>false/*,"disabled"=>$options['fieldDisabled'][0]*/])
        ->add('password', null,['attr' => ['class' => 'password-field']])
        ->add('satScore',null,['required'=>false/*,"disabled"=>$options['fieldDisabled'][0]*/])
        ->add('bankstatmentFilepdf',FileType::class,['mapped'=>false,'attr'     => [
            'class'=>'form-2-box form-control',
            
        
        ],
        'constraints' => [
            new File([
            'maxSize' => '2M',
            'mimeTypes' => [
                'application/pdf',
                'application/x-pdf',
                
                    ],
                'mimeTypesMessage' => 'Please upload a valid PDF document',
                ])
            ],"required"=>false/*,"disabled"=>$options['fieldDisabled'][0]*/
            ])
        ->add('transcriptfirstFilepdf',FileType::class,['mapped'=>false,'attr'     => [
                'class'=>'form-2-box form-control',
                
            
            ],
            'constraints' => [
                new File([
                'maxSize' => '2M',
                'mimeTypes' => [
                    'application/pdf',
                    'application/x-pdf',
                    
                        ],
                    'mimeTypesMessage' => 'Please upload a valid PDF document',
                    ])
                ],"required"=>false/*,"disabled"=>$options['fieldDisabled'][0]*/
                ])
        ->add('transcriptsecondFilepdf',FileType::class,['mapped'=>false,'attr'     => [
                    'class'=>'form-2-box form-control',
                    
                
                ],
                'constraints' => [
                    new File([
                    'maxSize' => '2M',
                    'mimeTypes' => [
                        'application/pdf',
                        'application/x-pdf',
                        
                            ],
                        'mimeTypesMessage' => 'Please upload a valid PDF document',
                        ])
                    ],"required"=>false/*,"disabled"=>$options['fieldDisabled'][0]*/
                    ])
        ->add('transcriptthirdFilepdf',FileType::class,['mapped'=>false,'attr'     => [
                        'class'=>'form-2-box form-control',
                        
                    
                    ],
                    'constraints' => [
                        new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'application/pdf',
                            'application/x-pdf',
                            
                                ],
                            'mimeTypesMessage' => 'Please upload a valid PDF document',
                            ])
                        ],"required"=>false/*,"disabled"=>$options['fieldDisabled'][0]*/
                        ])
        ->add('transcriptbacFilepdf',FileType::class,['mapped'=>false,'attr'     => [
                            'class'=>'form-2-box form-control',
                            
                        
                        ],
                        'constraints' => [
                            new File([
                            'maxSize' => '2M',
                            'mimeTypes' => [
                                'application/pdf',
                                'application/x-pdf',
                                
                                    ],
                                'mimeTypesMessage' => 'Please upload a valid PDF document',
                                ])
                            ],"required"=>false/*,"disabled"=>$options['fieldDisabled'][0]*/
                            ])
                
    ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' =>Candidature::class,
            'edit' => array(),
            'fieldDisabled' => array(),
        ]);
    }
}
