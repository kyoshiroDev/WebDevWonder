<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
  public function __construct(UserPasswordHasherInterface $passwordHasher)
  {
    $this->passwordHasher = $passwordHasher;
  }

  public function load(ObjectManager $manager): void
  {
    $greg = new User($this->passwordHasher);
    $greg
      ->setEmail("tahir.gregory@gmail.com")
      ->setUsername("Gregory")
      ->setPassword("password")
      ->setRoles(["ROLE_ADMIN", "ROLE_USER"]);
    $manager->persist($greg);
    $manager->flush();
  }
}
