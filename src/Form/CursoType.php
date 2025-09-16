<?php

namespace App\Form;

use App\Entity\Carrera;
use App\Entity\Curso;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CursoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nombre')
            ->add('horas')
            ->add('carrera', ChoiceType::class, [
                'choices' => $options["carreras"],
                'choice_value' => "id",
                'choice_label' => function (?Carrera $carrera): string {
                    return $carrera ? $carrera->getNombre() : "";
                },
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Curso::class,
            'carreras' => [],
        ]);
    }
}
