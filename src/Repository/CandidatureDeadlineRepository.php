<?php

namespace App\Repository;

use App\Entity\CandidatureDeadline;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method CandidatureDeadline|null find($id, $lockMode = null, $lockVersion = null)
 * @method CandidatureDeadline|null findOneBy(array $criteria, array $orderBy = null)
 * @method CandidatureDeadline[]    findAll()
 * @method CandidatureDeadline[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CandidatureDeadlineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CandidatureDeadline::class);
    }

    // /**
    //  * @return CandidatureDeadline[] Returns an array of CandidatureDeadline objects
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
    public function findOneBySomeField($value): ?CandidatureDeadline
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
