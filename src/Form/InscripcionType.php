<?php

namespace App\Form;

use App\Entity\Inscripcion;
use App\Entity\Alumno;
use App\Entity\Dictado;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class InscripcionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('alumno', EntityType::class, [
                'class' => Alumno::class,
                'choice_label' => function(Alumno $alumno) {
                    return $alumno->getApellido() . ', ' . $alumno->getNombre() . ' (' . $alumno->getDni() . ')';
                },
                'label' => 'Alumno',
                'placeholder' => 'Seleccionar alumno',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('dictado', EntityType::class, [
                'class' => Dictado::class,
                'choice_label' => function(Dictado $dictado) {
                    return $dictado->getNombre() . ' - ' . $dictado->getCurso()->getNombre();
                },
                'label' => 'Dictado',
                'placeholder' => 'Seleccionar dictado',
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Inscripcion::class,
        ]);
    }
}
