<?php

namespace App\Repository;

use App\Entity\AdressesClient;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;


/**
 * @method AdressesClient|null find($id, $lockMode = null, $lockVersion = null)
 * @method AdressesClient|null findOneBy(array $criteria, array $orderBy = null)
 * @method AdressesClient[]    findAll()
 * @method AdressesClient[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AdressesClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, AdressesClient::class);
    }

    // /**
    //  * @return AdressesClient[] Returns an array of AdressesClient objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?AdressesClient
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
