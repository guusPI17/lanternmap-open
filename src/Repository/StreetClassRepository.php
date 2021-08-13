<?php

namespace App\Repository;

use App\Entity\StreetClass;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StreetClass|null find($id, $lockMode = null, $lockVersion = null)
 * @method StreetClass|null findOneBy(array $criteria, array $orderBy = null)
 * @method StreetClass[]    findAll()
 * @method StreetClass[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StreetClassRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StreetClass::class);
    }

    // /**
    //  * @return StreetClass[] Returns an array of StreetClass objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?StreetClass
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
