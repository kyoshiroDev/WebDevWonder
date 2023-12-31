<?php

namespace App\Form;

use App\Entity\Post;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class PostTypeEdit extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
      ->add("title", TextType::class, [
        "label" => "Titre",
        "required" => false,
        "constraints" => [new Length([
          "min" => 0, "minMessage" => "Le titre ne doit pas faire plus de 150 caractères",
          "max" => 150, "maxMessage" => "Le titre ne doit pas faire plus de 150 caractères"]),]
      ])
      ->add("content", TextareaType::class, [
        "label" => "Contenu",
        "required" => true,
        "constraints" => [
          new Length([
            "min" => 5, "minMessage" => "Le contenu doit faire entre 5 et 320 caractères",
            "max" => 320,  "maxMessage" => "Le contenu doit faire entre 5 et 320 caractères"]),
          new NotBlank(["message" => 'Le contenu ne doit pas être vide !'])
        ]
      ])
      ->add("image", FileType::class, [
        "label" => "L'image",
        'mapped' => false,
        "required" => false,
        'constraints' => [
          new File([
            'maxSize' => '5M',
            'mimeTypes' => [
              'image/jpeg',
              "image/gif",
              "image/png",
              "image/svg+xml",
              "image/jpg",
              "image/webp"
            ],
            'mimeTypesMessage' => 'Veuillez proposer une image valide.',
          ])
        ],
      ]);
  }

  public function configureOptions(OptionsResolver $resolver)
  {
    $resolver->setDefaults([
      "data_class" => Post::class,
      'csrf_protection' => true,
      'csrf_field_name' => '_token',
      'csrf_token_id'   => 'post_item',
    ]);
  }
}
