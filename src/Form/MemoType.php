<?php

namespace App\Form;

use App\Entity\Memo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class MemoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre',
            ])
            ->add('contenu', TextareaType::class, [
                'label' => 'Contenu',
            ])
            ->add('createdAt', DateType::class, [
                'label' => 'Date de création',
                'widget' => 'single_text',
                'html5' => true,
            ])
            ->add('pdfFiles', FileType::class, [
                'label' => 'Fichiers PDF (PDF files)',
                'mapped' => false,
                'required' => false,
                'multiple' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'application/pdf',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un fichier PDF valide',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Memo::class,
        ]);
    }
}