<?php

namespace App\Form;

use App\Entity\Docente;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocenteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ingrese el nombre del docente'
                ]
            ])
            ->add('apellido', TextType::class, [
                'label' => 'Apellido',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ingrese el apellido del docente'
                ]
            ])
            ->add('correo', EmailType::class, [
                'label' => 'Correo Electrónico',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'docente@ejemplo.com'
                ]
            ])
            ->add('dni', IntegerType::class, [
                'label' => 'DNI',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => '12345678'
                ]
            ])
            ->add('especialidad', TextType::class, [
                'label' => 'Especialidad',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ej: Ingeniería de Software'
                ]
            ])
            ->add('titulo', TextType::class, [
                'label' => 'Título',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ej: Doctor en Ciencias de la Computación'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Docente::class,
        ]);
    }
}
