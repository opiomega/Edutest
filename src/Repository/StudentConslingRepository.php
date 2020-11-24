<?php

namespace App\Repository;

use App\Entity\StudentConsling;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method StudentConsling|null find($id, $lockMode = null, $lockVersion = null)
 * @method StudentConsling|null findOneBy(array $criteria, array $orderBy = null)
 * @method StudentConsling[]    findAll()
 * @method StudentConsling[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class StudentConslingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, StudentConsling::class);
    }

    // /**
    //  * @return StudentConsling[] Returns an array of StudentConsling objects
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
    public function findOneBySomeField($value): ?StudentConsling
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
