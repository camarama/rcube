<?php

namespace App\Repository;

use App\Entity\Lettre;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Lettre|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lettre|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lettre[]    findAll()
 * @method Lettre[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LettreRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lettre::class);
    }

    // /**
    //  * @return Lettre[] Returns an array of Lettre objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('l.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Lettre
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
