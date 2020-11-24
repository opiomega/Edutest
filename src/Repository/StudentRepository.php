<?php

namespace App\Repository;

use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @method Student|null find($id, $lockMode = null, $lockVersion = null)
 * @method Student|null findOneBy(array $criteria, array $orderBy = null)
 * @method Student[]    findAll()
 * @method Student[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 *
 */
class StudentRepository extends ServiceEntityRepository
{
    /**
     * @return Query
     */
    /**public function findAllVisibleQuery(Student $searsh):Query
    {
        $query= $this->findVisibleQuery();

        if ($searsh->getClass()){
            $query= $query
              ->andwhere('c.class like :name')
              ->setParameter('name','%'.$searsh->getClass().'%');
        }
      #  if ($searsh->getTeacher()){
       #     $query= $query
        #      ->andwhere('t.teacher like :firstname')
         #     ->setParameter('firstname','%'.$searsh->getTeacher().'%');
        #}
        if ($searsh->getId()){
            $query= $query
              ->andwhere('i.id like :id')
              ->setParameter('id','%'.$searsh->getId().'%');
        }
         return $query->getQuery();

    }*/
    public function __construct(ManagerRegistry $registry)
    {
       
        parent::__construct($registry, Student::class);
    }

    // /**
    //  * @return Student[] Returns an array of Student objects
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
    public function findOneBySomeField($value): ?Student
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
