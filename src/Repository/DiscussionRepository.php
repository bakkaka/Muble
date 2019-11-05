<?php

namespace App\Repository;

use App\Entity\Discussion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Discussion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Discussion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Discussion[]    findAll()
 * @method Discussion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiscussionRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Discussion::class);
    }
	
	 public function getDiscussionWithUser($user)
    {

        $qb = $this->createQueryBuilder('d')
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
        $qb->andWhere('d.user = :user')
            ->setParameter('user', $user)
        ;
        $qb->orderBy('d.createdAt', 'DESC')
        ;
        return $qb

            ->getQuery()

            ->getResult()

            ;
    }

	
	
	

    // /**
    //  * @return Discussion[] Returns an array of Discussion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('d.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Discussion
    {
        return $this->createQueryBuilder('d')
            ->andWhere('d.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
