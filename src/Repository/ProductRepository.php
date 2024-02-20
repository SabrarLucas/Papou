<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 *
 * @method Product|null find($id, $lockMode = null, $lockVersion = null)
 * @method Product|null findOneBy(array $criteria, array $orderBy = null)
 * @method Product[]    findAll()
 * @method Product[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    public function findProductByWord(string $query)
    {
        $qb = $this->createQueryBuilder('p');
        $qb
            ->where(
                $qb->expr()->andX(
                    $qb->expr()->orX(
                        $qb->expr()->like('p.name', ':query'),
                        $qb->expr()->like('p.description', ':query'),
                    )
                )
            )
            ->setParameter('query', '%' . $query . '%')
        ;
        return $qb
            ->getQuery()
            ->getResult();
    }

   public function findCategoryDesc(string $value): array
   {
       return $this->createQueryBuilder('p')
           ->andWhere('p.id_category = :val')
           ->setParameter('val', $value)
           ->orderBy('p.id', 'DESC')
           ->getQuery()
           ->getResult()
       ;
   }

   public function findAgeDesc(string $value): array
   {
       return $this->createQueryBuilder('p')
           ->andWhere('p.age = :val')
           ->setParameter('val', $value)
           ->orderBy('p.id', 'DESC')
           ->getQuery()
           ->getResult()
       ;
   }

   public function findSupplierDesc(string $value): array
   {
       return $this->createQueryBuilder('p')
           ->andWhere('p.id_supplier = :val')
           ->setParameter('val', $value)
           ->orderBy('p.id', 'DESC')
           ->getQuery()
           ->getResult()
       ;
   }

   public function findAllDesc(): array
   {
       return $this->createQueryBuilder('p')
           ->orderBy('p.id', 'DESC')
           ->getQuery()
           ->getResult()
       ;
   }

//    public function findOneBySomeField($value): ?Product
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
