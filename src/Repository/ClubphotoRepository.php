<?php

namespace App\Repository;

use App\Entity\Clubphoto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Clubphoto|null find($id, $lockMode = null, $lockVersion = null)
 * @method Clubphoto|null findOneBy(array $criteria, array $orderBy = null)
 * @method Clubphoto[]    findAll()
 * @method Clubphoto[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClubphotoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Clubphoto::class);
    }

    // /**
    //  * @return Clubphoto[] Returns an array of Clubphoto objects
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
    public function findOneBySomeField($value): ?Clubphoto
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
