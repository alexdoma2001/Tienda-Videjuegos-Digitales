<?php

namespace App\Repository;

use App\Entity\Clave;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Clave>
 *
 * @method Clave|null find($id, $lockMode = null, $lockVersion = null)
 * @method Clave|null findOneBy(array $criteria, array $orderBy = null)
 * @method Clave[]    findAll()
 * @method Clave[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ClaveRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Clave::class);
    }

//    /**
//     * @return Clave[] Returns an array of Clave objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('c.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Clave
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
