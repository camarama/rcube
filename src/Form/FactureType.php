<?php

namespace App\Form;

use App\Entity\Facturation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('date')
//            ->add('reference')
//            ->add('echeance')
            ->add('acompte', MoneyType::class, [
                'divisor' => 1,
                'currency' => false,
                'attr' => [
                    'class' => 'form-control',
                    'aria-describedby' => 'basic-addon2',
                    'placeholder' => 'Ex : 5 000'
                ]
            ])
            /*->add('solde', MoneyType::class, [
                'divisor' => 100,
                'currency' => false,
                'attr' => [
                    'class' => 'form-control',
                    'aria-describedby' => 'basic-addon2',
                    'placeholder' => 'Ex : 10 000'
                ]
            ])*/
//            ->add('devis')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Facturation::class,
        ]);
    }
}
