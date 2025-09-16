<?php

namespace App\Form;

use App\Entity\Curso;
use App\Entity\Dictado;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DictadoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fechaInicio')
            ->add('fechaFin')
            ->add('nombre')
            ->add('curso', EntityType::class, [
                'class' => Curso::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dictado::class,
        ]);
    }
}
