<?php

namespace App\Form;

use App\Entity\Curso;
use App\Entity\Carrera;
use App\Entity\Docente;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CursoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre', TextType::class, [
                'label' => 'Nombre del Curso',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ingrese el nombre del curso'
                ]
            ])
            ->add('horas', IntegerType::class, [
                'label' => 'Horas Cátedra',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ej: 40'
                ]
            ])
            ->add('carrera', EntityType::class, [
                'class' => Carrera::class,
                'choice_label' => 'nombre',
                'label' => 'Carrera',
                'placeholder' => 'Seleccione una carrera',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('docente', EntityType::class, [
                'class' => Docente::class,
                'choice_label' => 'nombreCompleto',
                'label' => 'Docente Responsable',
                'placeholder' => 'Seleccione un docente',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
                ]
            ])
            ->add('esObligatorio', CheckboxType::class, [
                'label' => 'Es Obligatorio',
                'required' => false,
                'attr' => [
                    'class' => 'form-check-input'
                ]
            ])
            ->add('tarifaMensual', MoneyType::class, [
                'label' => 'Tarifa Mensual',
                'required' => false,
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
            'data_class' => Curso::class,
        ]);
    }
}
