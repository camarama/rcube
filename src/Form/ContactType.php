<?php

namespace App\Form;

use App\Entity\Contact;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('entreprise', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Nom de votre entreprise*',
                ],
                'help' => '* Champ entreprise ne peut etre vide, min 2 caractères',
                'help_attr' => [
                    'class' => 'text-muted pl-2 font-italic'
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre nom*',
                ],
                'help' => '* Champ nom ne peut etre vide, min 2 caractères',
                'help_attr' => [
                    'class' => 'text-muted pl-2 font-italic'
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre adresse mail*',
                ],
                'help' => '* Veuillez taper une adresse mail valide',
                'help_attr' => [
                    'class' => 'text-muted pl-2 font-italic'
                ],
            ])
            ->add('message',TextareaType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre message*',
                    'rows' => 5
                ],
                'help' => '* Champ message doit contenir au moins 10 caractères',
                'help_attr' => [
                    'class' => 'text-muted pl-2 font-italic'
                ],
            ])
            ->add('objet', TextType::class, [
                'label' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Votre objet*',
                ],
                'help' => '* Champ objet ne peut être vide, min 2 caractères',
                'help_attr' => [
                    'class' => 'text-muted pl-2 font-italic'
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}
