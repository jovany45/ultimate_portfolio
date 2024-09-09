<?php
// src/Form/LivreDorEntryType.php

namespace App\Form;

use App\Entity\LivreDor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LivreDorEntryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('auteur', TextType::class, [
                'label' => 'Nom',
            ])
            ->add('mail', EmailType::class, [
                'label' => 'E-mail',
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message',
            ])
            ->add('media', FileType::class, [
                'label' => 'Télécharger un média (photo/vidéo)',
                'required' => false,
                'mapped' => false,
                'constraints' => [
                    new \Symfony\Component\Validator\Constraints\File([
                        'maxSize' => '100024M',
                        'mimeTypes' => [
                            'image/*',
                            'video/*',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger une image ou une vidéo valide',
                    ])
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => LivreDor::class,
        ]);
    }
}