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

    public function findProductByWord(string $query) // recupere les produit d'un mot ou un phrase donnee
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
    
    public function findCategoryDesc(string $value): array // recupere les produit par ordre decroissant d'une categorie donnee
    {
        return $this->createQueryBuilder('p')
        ->andWhere('p.id_category = :val')
        ->setParameter('val', $value)
        ->orderBy('p.id', 'DESC')
        ->getQuery()
        ->getResult()
        ;
    }
    
    public function findAgeDesc(string $value): array // recupere les produit par ordre decroissant d'une tranche age donnee
    {
        return $this->createQueryBuilder('p')
        ->andWhere('p.age = :val')
        ->setParameter('val', $value)
        ->orderBy('p.id', 'DESC')
        ->getQuery()
        ->getResult()
        ;
    }
    
    public function findSupplierDesc(string $value): array // recupere les produit par ordre decroissant d'un partenaire donnee
    {
        return $this->createQueryBuilder('p')
        ->andWhere('p.id_supplier = :val')
        ->setParameter('val', $value)
        ->orderBy('p.id', 'DESC')
        ->getQuery()
        ->getResult()
        ;
    }
    
    public function findAllDesc(): array // recupere les produit par ordre decroissant
    {
        return $this->createQueryBuilder('p')
        ->orderBy('p.id', 'DESC')
        ->getQuery()
        ->getResult()
        ;
    }
    
    public function findAllCategoryDesc(string $value): array // recupere les produit par ordre decroissant
    {
        return $this->createQueryBuilder('p')
        ->join('p.id_category', 'f')
        ->join('f.category', 'm')
        ->where('m.id = :val')
        ->setParameter('val', $value)
        ->orderBy('p.id', 'DESC')
        ->getQuery()
        ->getResult()
        ;
    }

   public function find20Max(): array // recupere les produit par ordre decroissant a moins de 20 euro (20max)
   {
       return $this->createQueryBuilder('p')
            ->where('p.price <= 20')
           ->orderBy('p.id', 'DESC')
           ->setMaxResults(20)
           ->getQuery()
           ->getResult()
       ;
   }

   public function findSupplierAndCategoryDesc(): array // recupere les produit par ordre decroissant d'un partenaire donnee
   {
       return $this->createQueryBuilder('p')
        ->select('p.id, p.name, p.description, p.price, p.state, p.age, p.created_at, c.name as category_name, s.company_name')
        ->join('p.id_category', 'c')
        ->join('p.id_supplier', 's')
       ->orderBy('p.id', 'DESC')
       ->getQuery()
       ->getResult()
       ;
   }
   

//    SELECT p.*, c.name, s.company_name FROM `product` p JOIN category c ON c.id = p.id_category_id JOIN supplier s ON s.id = p.id_supplier_id; 

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
