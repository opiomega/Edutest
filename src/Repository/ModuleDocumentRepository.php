<?php

namespace App\Repository;

use App\Entity\ModuleDocument;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ModuleDocument|null find($id, $lockMode = null, $lockVersion = null)
 * @method ModuleDocument|null findOneBy(array $criteria, array $orderBy = null)
 * @method ModuleDocument[]    findAll()
 * @method ModuleDocument[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ModuleDocumentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ModuleDocument::class);
    }

    // /**
    //  * @return ModuleDocument[] Returns an array of ModuleDocument objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('m.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ModuleDocument
    {
        return $this->createQueryBuilder('m')
            ->andWhere('m.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
