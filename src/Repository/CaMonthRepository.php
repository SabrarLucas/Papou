<?php

namespace App\Repository;

use App\Entity\CaMonth;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CaMonth>
 *
 * @method CaMonth|null find($id, $lockMode = null, $lockVersion = null)
 * @method CaMonth|null findOneBy(array $criteria, array $orderBy = null)
 * @method CaMonth[]    findAll()
 * @method CaMonth[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CaMonthRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CaMonth::class);
    }

    //    /**
    //     * @return CaMonth[] Returns an array of CaMonth objects
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

    //    public function findOneBySomeField($value): ?CaMonth
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
