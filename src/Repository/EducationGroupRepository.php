<?php

namespace App\Repository;

use App\Entity\EducationGroup;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method EducationGroup|null find($id, $lockMode = null, $lockVersion = null)
 * @method EducationGroup|null findOneBy(array $criteria, array $orderBy = null)
 * @method EducationGroup[]    findAll()
 * @method EducationGroup[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EducationGroupRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, EducationGroup::class);
    }

    // /**
    //  * @return EducationGroup[] Returns an array of EducationGroup objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('g.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?EducationGroup
    {
        return $this->createQueryBuilder('g')
            ->andWhere('g.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
