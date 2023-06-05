<?php

namespace App\Form;

use App\Entity\Users;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UsersType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('roles',ChoiceType::class,[
                    'choices'=>[
                        'Utilisateur'=>'ROLE_USER',
                        'Administrateur'=>'ROLE_USER'
                    ]
                ])->get('roles')
        ->addModelTransformer(new CallbackTransformer(
            function ($rolesAsArray) {
                 return count($rolesAsArray) ? $rolesAsArray[0]: null;
            },
            function ($rolesAsString) {
                 return [$rolesAsString];
            }
        ));
           
        //     ->add('roles',ChoiceType::class,[
        //         'choices'=>[
        //             'Utilisateur'=>'ROLE_USER',
        //             'Administrateur'=>'ROLE_USER'
        //         ]
        //     ])
     
        // ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Users::class,
        ]);
    }
}
