<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Author;
use App\Repository\BookRepository;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use App\Form\AuthorType;

#[Route('/author', name: 'author')]

class AuthorController extends AbstractController
{
    #[Route('/author', name: 'app_author')]
    public function index(): Response
    {
        return $this->render('author/index.html.twig', [
            'controller_name' => 'AuthorController',
        ]);
    }
    #[Route('/fetch', name: 'fetch')]
    public function fetch(AuthorRepository $repo): Response{
        $result=$repo->listAuthorByEmail();
        return $this->render('author/listAuthor.html.twig',[
            'response'=>$result,
        ]);
    }

   


    #[Route('/fetch2', name: 'fetch2')]
    public function fetch2(ManagerRepository $mr): Response{
        $repo=$mr->getRepository(Author::class);
        $result=$repo->findAll();
        return $this->render('author/listAuthor.html.twig',[
            'response'=>$result,
        ]);
    }
    #[Route('/addF', name: 'addF')]
    public function addF(ManagerRegistry $mr, AuthorRepository $repo, Request $req): Response
    {
        $s = new Author();
        $form = $this->createForm(AuthorType::class, $s);
        $form->handleRequest($req);
        if ($form->isSubmitted()) {
            $em = $mr->getManager();
            $em->persist($s);
            $em->flush();
            return $this->redirectToRoute('authorfetch');
        }
        return $this->render('author/add.html.twig', [
            'f' => $form->createView(),
        ]);
    }

    #[Route('/remove/{id}', name: 'remove')]
    public function remove(ManagerRegistry $mr , $id,AuthorRepository $repo):Response
    {
      $author=$repo->find($id);

      $em=$mr->getManager();
      $em->remove($author);
      $em->flush();
      return $this->redirectToRoute('authorfetch');

      return new Response('authorremove');
    }
 
 


    #[Route('/edit/{id}', name: 'edit_author')]
public function edit(ManagerRegistry $mr, $id, AuthorRepository $repo, Request $req): Response
{
    $author = $repo->find($id);

    if (!$author) {
        throw $this->createNotFoundException('author not found');
    }

    $form = $this->createForm(AuthorType::class, $author);
    $form->handleRequest($req);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $mr->getManager();
        $em->flush();
        return $this->redirectToRoute('authorfetch');
    }

    return $this->render('author/edit.html.twig', [
        'f' => $form->createView(),
    ]);
}
}
