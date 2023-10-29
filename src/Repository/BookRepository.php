<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Book>
 *
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }
    public function searchBookByRef(int $ref)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.ref = :ref')
            ->setParameter('ref', $ref)
            ->getQuery()
            ->getOneOrNullResult();
    }
    public function booksListByAuthors()
{
    return $this->createQueryBuilder('b')
        ->leftJoin('b.Author', 'a')
        ->orderBy('a.username', 'ASC')
        ->getQuery()
        ->getResult();
}
public function findPublishedBooksByAuthorWithMoreThan10Books()
{
    $qb = $this->createQueryBuilder('b')
        ->select('b', 'a')
        ->join('b.Author', 'a')
        ->where('b.PublicationDate < :date')
        ->having('COUNT(b.Author) > 10');

    $qb->setParameter('date', new \DateTime('2023-01-01'));

    return $qb->getQuery()->getResult();
}
public function updateCategoryFromScienceFictionToRomance()
{
    $qb = $this->createQueryBuilder('b');
    
    $qb->update('App\Entity\Book', 'b')
        ->set('b.category', ':newCategory')
        ->where('b.category = :oldCategory')
        ->setParameter('newCategory', 'Romance')
        ->setParameter('oldCategory', 'ded');
    
    return $qb->getQuery()->execute();
}


public function countBooksInRomanceCategory()
{
    $entityManager = $this->getEntityManager();

    $query = $entityManager->createQuery('
        SELECT COUNT(b.ref) as bookCount
        FROM App\Entity\Book b
        WHERE b.category = :category
    ');

    $query->setParameter('category', 'Romance');

    return $query->getSingleScalarResult();
}

public function findBooksPublishedBetweenDates($startDate, $endDate)
{
    $entityManager = $this->getEntityManager();

    $query = $entityManager->createQuery('
        SELECT b
        FROM App\Entity\Book b
        WHERE b.PublicationDate BETWEEN :startDate AND :endDate
    ');

    $query->setParameter('startDate', $startDate);
    $query->setParameter('endDate', $endDate);

    return $query->getResult();
}
//    /**
//     * @return Book[] Returns an array of Book objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Book
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
