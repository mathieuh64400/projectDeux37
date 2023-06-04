<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Departement;
use App\Entity\Produit;
use App\Entity\Region;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class)
            ->add('description',TextareaType::class,[
                'label'=>false
            ])
            // partie traitant e l'image mutiple
            // ->add('images',FileType::class,[
            //     'label'=>false,
            //     'multiple'=>true,
            //     'mapped'=>false,
            //     'required'=>false
            // ])

            // // partie traitant des regions 
            // ->add(
            //     'regions',
            //     EntityType::class,
            //     [
            //         'mapped' => false,
            //         'class' => Region::class,
            //         'choice_label' => 'nom',
            //         'label' => 'choix de la region d\'origine du produit'
            //     ]
            // )
            ->add('departement', EntityType::class,[
                'placeholder'=>'Département',
                'class' => Departement::class,
                'choice_label' => 'nom',
                'label' => 'choix du departement d\'origine du produit'
            ])
            ->add('categorie',EntityType::class,[
                'placeholder'=>'libellé',
                'class' => Categorie::class,
                'choice_label' => 'libelle',
                'label' => 'categorie du Produit'
            ]);

            // ->add('submit', SubmitType::class);

        // $formModifier = function (FormInterface $form, Region $region = null) {
        //     $departement = null === $region ? [] : $region->getDepartement();
               
        //     $form->add('departement', EntityType::class, [
        //         'class' => Departement::class,
        //         'choices' => $departement,
        //         'choice_label' => 'nom',
        //         'placeholder' => 'Département lié a votre région ',
        //         'label' => 'le departement associé',
        //         'required' => true,
        //     ]);
           
        // };


        // $builder->get('regions')->addEventListener(
        //     FormEvents::POST_SUBMIT,
        //     function (FormEvent $event) use ($formModifier) {
        //         $region = $event->getForm()->getData();
        //         $formModifier($event->getForm()->getParent(), $region);}
               


        // );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
