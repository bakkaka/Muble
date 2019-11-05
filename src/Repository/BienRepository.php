<?php

namespace App\Repository;

use App\Entity\Bien;
use App\Entity\PropertySearch;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Property|null find(Property[]$id, $lockMode = null, $lockVersion = null)
 * @method Property|null findOneBy(array $criteria, array $orderBy = null)
 * @method Property[]    findAll()
 * @method Property[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BienRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Bien::class);
    }
	
	 public function findAllBiens() {
	 
	     $qb = $this->createQueryBuilder('b')
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
      
        ;
        $qb->orderBy('b.created_at', 'DESC')
        ;
        return $qb

            ->getQuery()

            ->getResult()

            ;
	    
	 }
	
	 public function getBiensWithUser($user)
    {

        $qb = $this->createQueryBuilder('b')
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
        $qb->andWhere('b.user = :user')
            ->setParameter('user', $user)
        ;
        $qb->orderBy('b.created_at', 'DESC')
        ;
        return $qb

            ->getQuery()

            ->getResult()

            ;
    }

    /**
     * @return Query
     */
    public function findAllVisibleQuery(PropertySearch $search): Query
    {
        $query = $this->findVisibleQuery();

        if ($search->getMaxPrice()) {
            $query = $query
                ->andWhere('b.price <= :maxprice')
                ->setParameter('maxprice', $search->getMaxPrice());
        }

        if ($search->getMinSurface()) {
            $query = $query
                ->andWhere('b.surface >= :minsurface')
                ->setParameter('minsurface', $search->getMinSurface());
        }

        if ($search->getLat() && $search->getLng() && $search->getDistance()) {
            $query = $query
                ->select('b')
                ->andWhere('(6353 * 2 * ASIN(SQRT( POWER(SIN((p.lat - :lat) *  pi()/180 / 2), 2) +COS(p.lat * pi()/180) * COS(:lat * pi()/180) * POWER(SIN((p.lng - :lng) * pi()/180 / 2), 2) ))) <= :distance')
                ->setParameter('lng', $search->getLng())
                ->setParameter('lat', $search->getLat())
                ->setParameter('distance', $search->getDistance());
        }

        if ($search->getOptions()->count() > 0) {
            $k = 0;
            foreach($search->getOptions() as $option) {
                $k++;
                $query = $query
                    ->andWhere(":option$k MEMBER OF p.options")
                    ->setParameter("option$k", $option);
            }
        }

        return $query->getQuery();
    }

    /**
     * @return Property[]
     */
    public function findLatest(): array
    {
        return $this->findVisibleQuery()
            ->setMaxResults(4)
            ->getQuery()
            ->getResult();
    }

    private function findVisibleQuery(): QueryBuilder
    {
        return $this->createQueryBuilder('b')
            ->where('b.sold = false');
    }


//    /**
//     * @return Property[] Returns an array of Property objects
//     */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Property
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
