<?php

namespace App\Form;

use App\Entity\Locality;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class LocalityType extends AbstractType
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
            ->add('fileDataMovement', FileType::class, [
                'label' => 'Файл: ',
                'mapped' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => [
                            'text/plain',
                        ],
                        'mimeTypesMessage' => 'Требуется один из данных форматов: {{ types }}',
                        'uploadErrorMessage' => 'Ошибка при загрузке файла',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Locality::class,
        ]);
    }
}
