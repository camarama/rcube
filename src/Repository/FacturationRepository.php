<?php

namespace App\Repository;

use App\Entity\Facturation;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Facturation|null find($id, $lockMode = null, $lockVersion = null)
 * @method Facturation|null findOneBy(array $criteria, array $orderBy = null)
 * @method Facturation[]    findAll()
 * @method Facturation[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FacturationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Facturation::class);
    }

     /**
      * @return Facturation[] Returns an array of Facturation objects
      */

    public function findByDevis($value)
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.id = :val')
            ->setParameter('val', $value)
            ->join('f.devis', 'd')
            ->addSelect('d')
            ->join('d.client', 'c')
            ->addSelect('c')
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?Facturation
    {
        return $this->createQueryBuilder('f')
            ->andWhere('f.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
