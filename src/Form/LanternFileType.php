<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\File;

class LanternFileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lanternFile', FileType::class, [
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
}
