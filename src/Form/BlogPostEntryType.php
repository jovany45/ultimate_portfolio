<?php

namespace App\Form;

use App\Entity\BlogPost;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BlogPostEntryType extends AbstractType
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
            ->add('media', FileType::class, [
                'label' => 'Ajouter une photo ou vidéo',
                'required' => false,
                'mapped' => false, // ce champ n'est pas lié à une propriété de l'entité
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                            'image/gif',
                            'video/mp4',
                            'video/webm',
                            'video/ogg',
                        ],
                        'mimeTypesMessage' => 'Veuillez télécharger un fichier valide (JPEG, PNG, GIF, MP4, WebM, OGG)',
                    ]
                ),
            ],
            ])
            
            ->add('createdAt', DateType::class, [
                'label' => 'Date de création',
                'widget' => 'single_text',
                'html5' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => BlogPost::class,
        ]);
    }
}