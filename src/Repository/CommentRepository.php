<?php

namespace App\Repository;

use App\Entity\Comment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Comment|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comment|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comment[]    findAll()
 * @method Comment[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CommentRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Comment::class);
    }

    public function getCommentWithArticle($article)
    {
        $qb = $this->createQueryBuilder('c');

        $qb->leftJoin('c.article', 'a')
            ->addSelect('a')
            //->leftJoin('c.user', 'u')
            // ->addSelect('u')
        ;
        $qb->andWhere('c.article = :article')
            ->setParameter('article', $article)
            ->orderBy('c.createdAt', 'DESC');
        return $qb
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string|null $term
     * @return Comment[]
     */
    public function findAllWithSearch(?string $term)
    {
        $qb = $this->createQueryBuilder('c');
        if ($term) {
            $qb->andWhere('c.content LIKE :term OR c.authorName LIKE :term')
                ->setParameter('term', '%' . $term . '%');
        }
        return $qb
            ->orderBy('c.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * @param string|null $term
     * @return QueryBuilder
     */
    public function getWithSearchQueryBuilder(?string $term):QueryBuilder
    {
        $qb = $this->createQueryBuilder('c');
        if ($term) {
            $qb->andWhere('c.content LIKE :term OR c.authorName LIKE :term')
                ->setParameter('term', '%' . $term . '%');
        }
        return $qb
            ->orderBy('c.createdAt', 'DESC')
            ;
        // ->getQuery()
        //->getResult();
    }


    // /**
    //  * @return Comment[] Returns an array of Comment objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Comment
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
