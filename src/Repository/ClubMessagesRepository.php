<?php

namespace App\Repository;

use App\Entity\ClubMessages;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ClubMessages|null find($id, $lockMode = null, $lockVersion = null)
 * @method ClubMessages|null findOneBy(array $criteria, array $orderBy = null)
 * @method ClubMessages[]    findAll()
 * @method ClubMessages[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClubMessagesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ClubMessages::class);
    }

    // /**
    //  * @return ClubMessages[] Returns an array of ClubMessages objects
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
    public function findOneBySomeField($value): ?ClubMessages
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
