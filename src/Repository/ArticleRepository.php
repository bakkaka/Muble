<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Article::class);
    }
	
	 public function getArticleWithAuthor($author)
    {

        $qb = $this->createQueryBuilder('a')
            //->leftJoin('a.gender', 'g')
            //->addSelect('g')
            //->leftJoin('a.cities', 'c')
           // ->addSelect('c')
            //->leftJoin('a.specialite', 's')
            //->addSelect('s')
            //->leftJoin('a.image', 'i')
            //->addSelect('i')
            //->leftJoin('a.articlecategories', 'ac')
            //->addSelect('ac')

        ;
        $qb->andWhere('a.author = :author')
            ->setParameter('author', $author)
        ;
        $qb->orderBy('a.createdAt', 'DESC')
        ;
        return $qb

            ->getQuery()

            ->getResult()

            ;
    }

    // /**
    //  * @return Article[] Returns an array of Article objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
