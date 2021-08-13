<?php

namespace App\Repository;

use App\Entity\Lantern;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Lantern|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lantern|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lantern[]    findAll()
 * @method Lantern[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LanternRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lantern::class);
    }

    // /**
    //  * @return Lantern[] Returns an array of Lantern objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Lantern
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
