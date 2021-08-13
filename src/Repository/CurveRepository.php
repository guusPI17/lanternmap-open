<?php

namespace App\Repository;

use App\Entity\Curve;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Curve|null find($id, $lockMode = null, $lockVersion = null)
 * @method Curve|null findOneBy(array $criteria, array $orderBy = null)
 * @method Curve[]    findAll()
 * @method Curve[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CurveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Curve::class);
    }

    // /**
    //  * @return Curve[] Returns an array of Curve objects
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
    public function findOneBySomeField($value): ?Curve
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
