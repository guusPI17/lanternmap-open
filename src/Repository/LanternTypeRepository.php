<?php

namespace App\Repository;

use App\Entity\LanternType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method LanternType|null find($id, $lockMode = null, $lockVersion = null)
 * @method LanternType|null findOneBy(array $criteria, array $orderBy = null)
 * @method LanternType[]    findAll()
 * @method LanternType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LanternTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LanternType::class);
    }

    // /**
    //  * @return LanternType[] Returns an array of LanternType objects
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
    public function findOneBySomeField($value): ?LanternType
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
