<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Form\PostTypeEdit;
use App\Repository\PostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostController extends AbstractController
{
  #[Route('/', name: "app_post")]
  public function index(Request $request, PostRepository $repository): Response
  {
    $search = $request->request->get("search"); // $_POST["search"]
    $posts = $repository->findAll(); // SELECT * FROM `post`;
    if ($search) {
      $posts = $repository->findBySearch($search); // SELECT * FROM `post` WHERE title LIKE :search;
    }

    return $this->render('post/index.html.twig', [
      "posts" => $posts
    ]);
  }

  #[Route('/post/new', name: "app_post_new")]
  public function create(Request $request, ManagerRegistry $doctrine, SluggerInterface $slugger): Response
  {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    $post = new Post();
    $form = $this->createForm(PostType::class, $post);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $image = $form->get('image')->getData();

      if ($image) {
        $originalFilename = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid() . '.' . $image->guessExtension();
        try {
          $image->move(
            $this->getParameter('uploads'),
            $newFilename
          );
        } catch (FileException $e) {
          dump($e);
        }
        $post->setImage($newFilename);
      }

      $post->setUser($this->getUser());
      $post->setPublishedAt(new \DateTime());
      $em = $doctrine->getManager();
      $em->persist($post);
      $em->flush();
      return $this->redirectToRoute("app_post");
    }
    return $this->render('post/form.html.twig', [
      "post_form" => $form->createView()
    ]);
  }

  #[Route('/post/edit/{id}', name: "app_post_edit")]

  public function update(Request $request, Post $post, ManagerRegistry $doctrine): Response
  {

    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    if ($this->getUser() !== $post->getUser()) {
      $this->addFlash("error", "Vous ne pouvez pas modifier une publication qui ne vous appartient pas.");
      return $this->redirectToRoute("app_post");
      // throw new AccessDeniedException("Vous n'avez pas accès à cette fonctionnalité.");
    }
    $form = $this->createForm(PostTypeEdit::class, $post);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $em = $doctrine->getManager();
      $em->flush();
      return $this->redirectToRoute("app_post");
    }
    return $this->render('post/form-edit.html.twig', [
      "post_form" => $form->createView(),
    ]);
  }

  #[Route('/post/delete/{id<\d+>}', name: "delete")]
  public function delete(Post $post, ManagerRegistry $doctrine): Response
  {
    $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
    if ($this->getUser() !== $post->getUser()) {
      $this->addFlash("error", "Vous ne pouvez pas supprimer une publication qui ne vous appartient pas.");
      return $this->redirectToRoute("app_post");
    }
    $em = $doctrine->getManager();
    $em->remove($post);
    $em->flush();
    return $this->redirectToRoute("app_post");
  }
}
