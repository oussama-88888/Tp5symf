<?php

namespace App\Controller;

use App\Entity\Book;
use App\Repository\BookRepository;
use App\Repository\AuthorRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Form\BookType;

#[Route('/book', name: 'book')]

class BookController extends AbstractController
{
    #[Route('/book', name: 'app_book')]
    public function index(): Response
    {
        return $this->render('book/index.html.twig', [
            'controller_name' => 'BookController',
        ]);
    }
   
    #[Route('/fetch', name: 'fetch')]
public function fetch(Request $request, BookRepository $repo): Response {
    $searchRef = $request->query->get('ref'); // Récupérez la référence à partir de la requête GET
   
    if ($searchRef) {
        $foundBook = $repo->searchBookByRef((int)$searchRef);

        if ($foundBook) {
            $result = [$foundBook];
        } else {
            $result = []; // Aucune correspondance trouvée
        }
    } else {
        $result = $repo->findAll();
    }

    return $this->render('book/list.html.twig', [
        'response' => $result,
        'searchRef' => $searchRef, // Passez la référence recherchée à la vue
    ]);
}
#[Route('/fetchbyauteur', name: 'fetchbyauteur')]
public function booksListByAuthors(Request $request, BookRepository $repo): Response
{
    $books = $repo->booksListByAuthors();

    return $this->render('book/list.html.twig', [
        'response' => $books,
    ]);
}
#[Route('/fetchCond', name: 'fetchCond')]
public function publishedBooksByAuthorsWithMoreThan10Books(Request $request, BookRepository $repo): Response
{
    $books = $repo->findPublishedBooksByAuthorWithMoreThan10Books();

    return $this->render('book/list.html.twig', [
        'response' => $books,
    ]);
}
#[Route('/updateCat', name: 'updateCat')]
public function updateCategoryAction(BookRepository $repo)
{
    $updatedCount = $repo->updateCategoryFromScienceFictionToRomance();

    return new Response("Mise à jour effectuée. Nombre de livres mis à jour : " . $updatedCount);
}

#[Route('/nblivre', name: 'nblivre')]

public function countBooksInRomanceCategoryAction(BookRepository $repo)
{
    $bookCount = $repo->countBooksInRomanceCategory();

    return new Response("Nombre de livres dans la catégorie Romance : " . $bookCount);
}


#[Route('/listedate', name: 'listedate')]
public function findBooksPublishedBetweenDatesAction(BookRepository $repo)
{
    $startDate = new \DateTime('2014-01-01');
    $endDate = new \DateTime('2018-12-31');
    $books = $repo->findBooksPublishedBetweenDates($startDate, $endDate);

    return $this->render('book/list.html.twig', [
        'response' => $books,
    ]);
}
//2eme methode tnajem tji fl examen kmala !!!!!!!!!!!!!!

    #[Route('/fetch2', name: 'fetch2')]
    public function fetch2(ManagerRepository $mr): Response{
        $repo=$mr->getRepository(Book::class);
        $result=$repo->findAll();
        return $this->render('book/list.html.twig',[
            'response'=>$result,
        ]);
    }


    #[Route('/addF', name: 'addF')]
public function addF(ManagerRegistry $mr, BookRepository $repo, Request $req): Response
{
    $s = new Book();
    $form = $this->createForm(BookType::class, $s);
    $form->handleRequest($req);
    if ($form->isSubmitted()) {
        $em = $mr->getManager();
        $em->persist($s);
        $em->flush();
        return $this->redirectToRoute('bookfetch');
    }
    return $this->render('book/add.html.twig', [
        'f' => $form->createView(),
    ]);
}

    #[Route('/remove/{id}', name: 'remove')]
    public function remove(ManagerRegistry $mr , $id,BookRepository $repo):Response
    {
      $book=$repo->find($id);

      $em=$mr->getManager();
      $em->remove($book);
      $em->flush();
      return $this->redirectToRoute('bookfetch');

      return new Response('bookremove');
    }
 
 


    #[Route('/edit/{id}', name: 'edit_book')]
public function edit(ManagerRegistry $mr, $id, BookRepository $repo, Request $req): Response
{
    $book = $repo->find($id);

    if (!$book) {
        throw $this->createNotFoundException('Book not found');
    }

    $form = $this->createForm(BookType::class, $book);
    $form->handleRequest($req);

    if ($form->isSubmitted() && $form->isValid()) {
        $em = $mr->getManager();
        $em->flush();
        return $this->redirectToRoute('bookfetch');
    }

    return $this->render('book/edit.html.twig', [
        'f' => $form->createView(),
    ]);
}


}
