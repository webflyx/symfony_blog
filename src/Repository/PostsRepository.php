<?php

namespace App\Repository;

use App\Entity\Posts;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Posts>
 *
 * @method Posts|null find($id, $lockMode = null, $lockVersion = null)
 * @method Posts|null findOneBy(array $criteria, array $orderBy = null)
 * @method Posts[]    findAll()
 * @method Posts[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostsRepository extends ServiceEntityRepository
{
    private $paginator;

    public function __construct(ManagerRegistry $registry, PaginatorInterface $paginator)
    {
        parent::__construct($registry, Posts::class);
        $this->paginator = $paginator;
    }

    public function findAllPosts(int $page) 
    {
        $dbquery = $this->createQueryBuilder('p')
        // ->leftJoin('p.author', 'a')
        // ->addSelect('a')
        ->getQuery()
        ->getResult();
        
        return $this->paginator->paginate($dbquery, $page, 3);
    }

    public function findAllPostsByUser(int $page, $userId) 
    {
        $dbquery = $this->createQueryBuilder('p')
            ->leftJoin('p.author', 'a')
            ->where('p.author = :id')
            ->setParameter('id', $userId)
            ->getQuery()
            ->getResult();
        
        return $this->paginator->paginate($dbquery, $page, 3);
    }

//    /**
//     * @return Posts[] Returns an array of Posts objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Posts
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
