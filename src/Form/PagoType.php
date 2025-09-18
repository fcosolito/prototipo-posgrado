<?php

namespace App\Form;

use App\Entity\Pago;
use App\Entity\Cuota;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PagoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cuota', EntityType::class, [
                'class' => Cuota::class,
                'choice_label' => function(Cuota $cuota) {
                    return $cuota->getAlumno()->getApellido() . ', ' . 
                           $cuota->getAlumno()->getNombre() . ' - ' . 
                           $cuota->getFechaVencimiento()->format('d/m/Y') . ' - $' . 
                           number_format($cuota->getValor(), 2);
                },
                'label' => 'Cuota',
                'placeholder' => 'Seleccionar cuota',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('valor', MoneyType::class, [
                'label' => 'Valor del Pago',
                'currency' => 'ARS',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '0.00'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pago::class,
        ]);
    }
}
