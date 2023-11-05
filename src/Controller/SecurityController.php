<?php

namespace App\Controller;

use App\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
  #[Route('/login', name: 'app_login')]
  public function login(AuthenticationUtils $authenticationUtils, Request $request): Response
  {
    $error = $authenticationUtils->getLastAuthenticationError();
    $lastUsername = $authenticationUtils->getLastUsername();

    $form = $this->createForm(LoginType::class);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
    }

    return $this->render('security/login.html.twig', [
      'error' => $error,
      'last_username' => $lastUsername,
      "form" => $form->createView()
    ]);
  }

  #[Route('/logout', name: 'app_logout')]
  public function logout()
  {
    throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
  }
}
