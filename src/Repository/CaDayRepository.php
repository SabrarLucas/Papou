<?php

namespace App\Repository;

use App\Entity\CaDay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<CaDays>
 *
 * @method CaDay|null find($id, $lockMode = null, $lockVersion = null)
 * @method CaDay|null findOneBy(array $criteria, array $orderBy = null)
 * @method CaDay[]    findAll()
 * @method CaDay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CaDayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CaDay::class);
    }

    public function findCaDay(): array
    {
        $currentMonth = (new \DateTime())->format('m'); // Obtient le mois actuel au format numérique (ex: "03" pour mars)
    
        return $this->createQueryBuilder('c')
            ->andWhere('c.month = :currentMonth')
            ->setParameter('currentMonth', $currentMonth)
            ->getQuery()
            ->getResult();
    }

    //    SELECT * FROM `ca_day` ca WHERE ca.month = MONTH(NOW()); 

    //    public function findOneBySomeField($value): ?CaDays
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
