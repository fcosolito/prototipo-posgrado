<?php

namespace App\Form;

use App\Entity\Alumno;
use App\Entity\Inscripcion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RegNotaCursoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('alumno', ChoiceType::class, [
                'choices' => $options["inscripciones"],
                'choice_value' => "id",
                'choice_label' => function (?Inscripcion $inscripcion): string {
                    $alumno = $inscripcion ? $inscripcion->getAlumno() : null;
                    return $alumno ? $alumno->getApellido()." ".$alumno->getNombre()." dni ".$alumno->getDni() : "";
                }
            ])
            ->add('valor', IntegerType::class)
            ->add('registrar', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            "inscripciones" => [],
        ]);
    }
}
