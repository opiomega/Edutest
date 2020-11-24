<?php

namespace App\Repository;

use App\Entity\Educationlevel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Educationlevel|null find($id, $lockMode = null, $lockVersion = null)
 * @method Educationlevel|null findOneBy(array $criteria, array $orderBy = null)
 * @method Educationlevel[]    findAll()
 * @method Educationlevel[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EducationlevelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Educationlevel::class);
    }

    // /**
    //  * @return Educationlevel[] Returns an array of Educationlevel objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Educationlevel
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
