<?php

namespace App\Repository;

use App\Entity\Weeks;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Weeks|null find($id, $lockMode = null, $lockVersion = null)
 * @method Weeks|null findOneBy(array $criteria, array $orderBy = null)
 * @method Weeks[]    findAll()
 * @method Weeks[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WeeksRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Weeks::class);
    }

    // /**
    //  * @return Weeks[] Returns an array of Weeks objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('w.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Weeks
    {
        return $this->createQueryBuilder('w')
            ->andWhere('w.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
