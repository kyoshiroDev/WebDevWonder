<?php

namespace App\Form;

use App\Entity\User;
use Doctrine\DBAL\Types\JsonType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContext;

class UserType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    $builder
      ->add('username', TextType::class, [
        "label" => "Pseudo",
        "required" => true,
      ])
      ->add('email', EmailType::class, [
        "label" => "Adresse mail",
        "required" => true,
      ])
      ->add('password', PasswordType::class, [
        "label" => "mot de passe",
        "required" => true
      ])
      ->add('confirm_password', PasswordType::class, [
        "label" => "Confirmer le mot de passe",
        "required" => true,
        "constraints" => [
          new Callback(['callback' => function ($value, ExecutionContext $ec) {
            if ($ec->getRoot()['password']->getViewData() !== $value) {
              $ec->addViolation("Les mots de passe doivent être identique !");
            }
          }])
        ]
      ]);
  }

  public function configureOptions(OptionsResolver $resolver): void
  {
    $resolver->setDefaults([
      'data_class' => User::class,
      'csrf_protection' => true,
      'csrf_field_name' => '_token',
      'csrf_token_id'   => 'task_item',
    ]);
  }
}
