<?php

namespace App\Repository;

use App\Entity\PrestationType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PrestationType|null find($id, $lockMode = null, $lockVersion = null)
 * @method PrestationType|null findOneBy(array $criteria, array $orderBy = null)
 * @method PrestationType[]    findAll()
 * @method PrestationType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PrestationTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PrestationType::class);
    }

    // /**
    //  * @return PrestationType[] Returns an array of PrestationType objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PrestationType
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
