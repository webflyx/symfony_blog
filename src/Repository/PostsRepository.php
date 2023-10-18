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

    public function isLiked($authUser, $postId): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.id')
            ->andWhere('p.id = :id')
            ->andWhere('usersLiked.id = :authUser')
            ->innerJoin('p.usersLiked', 'usersLiked')
            ->setParameter('id', $postId)
            ->setParameter('authUser', $authUser)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    public function isDisliked($authUser, $postId): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.id')
            ->andWhere('p.id = :id')
            ->andWhere('usersDisliked.id = :authUser')
            ->innerJoin('p.usersDisliked', 'usersDisliked')
            ->setParameter('id', $postId)
            ->setParameter('authUser', $authUser)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    public function topPost(): array
    {
        return $this->createQueryBuilder('p')
            ->select('p AS post')
            ->innerJoin('p.usersLiked', 'l')
            ->groupBy('p')
            ->addSelect('COUNT(l) AS likes')
            ->orderBy('likes', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();
    }

    public function searchQuery(string $query): array
    {
        return $this->createQueryBuilder('p')
            ->select('p.title')
            ->addSelect('p.id')
            ->where('p.title LIKE :query')
            //->orWhere('p.content LIKE :query')
            ->setParameter('query', '%'.$query.'%')
            ->getQuery()
            ->getResult();
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
