<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Category>
 *
 * @method Category|null find($id, $lockMode = null, $lockVersion = null)
 * @method Category|null findOneBy(array $criteria, array $orderBy = null)
 * @method Category[]    findAll()
 * @method Category[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

   /**
    * @return Category[] Returns an array of Category objects
    */
   public function findParentCategory(): array
   {
       return $this->createQueryBuilder('c')
            ->select('m.name, m.id, m.photo, m.description')
            ->join('c.category', 'm')
            ->groupBy('m.name')
           ->getQuery()
           ->getResult()
       ;
   }

// SELECT m.* FROM `category` m JOIN category f ON m.id = f.category_id GROUP BY m.name; 

//    public function findOneBySomeField($value): ?Category
//    {
//        return $this->createQueryBuilder('c')
//            ->andWhere('c.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
