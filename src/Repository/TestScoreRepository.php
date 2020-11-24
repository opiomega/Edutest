<?php

namespace App\Repository;

use App\Entity\TestScore;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TestScore|null find($id, $lockMode = null, $lockVersion = null)
 * @method TestScore|null findOneBy(array $criteria, array $orderBy = null)
 * @method TestScore[]    findAll()
 * @method TestScore[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TestScoreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TestScore::class);
    }

    // /**
    //  * @return TestScore[] Returns an array of TestScore objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TestScore
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
