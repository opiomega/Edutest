<?php

namespace App\Repository;

use App\Entity\TypeCourse;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TypeCourse|null find($id, $lockMode = null, $lockVersion = null)
 * @method TypeCourse|null findOneBy(array $criteria, array $orderBy = null)
 * @method TypeCourse[]    findAll()
 * @method TypeCourse[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TypeCourseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TypeCourse::class);
    }

    // /**
    //  * @return TypeCourse[] Returns an array of TypeCourse objects
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
    public function findOneBySomeField($value): ?TypeCourse
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
