<?php

namespace App\Form;

use App\Entity\Locality;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LocalityGenerationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add(
                'editLocality',
                EntityType::class,
                [
                    'mapped' => false,
                    'class' => Locality::class,
                    'choice_label' => 'name',
                    'placeholder' => 'Выберите населенный пункт',
                ]
            )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Locality::class,
        ]);
    }
}
