<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
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
    
    public function findCategoryDesc(int $page, string $value, int $limit = 8): array // recupere les produit par ordre decroissant d'une categorie donnee
    {
        $limit = abs($limit);

        $result = [];

        $query = $this->createQueryBuilder('p')
        ->andWhere('p.id_category = :val')
        ->setParameter('val', $value)
        ->orderBy('p.id', 'DESC')
        ->setMaxResults($limit)
        ->setFirstResult(($page * $limit) - $limit);
        
        $paginator = new Paginator($query);

        $data = $paginator->getQuery()->getResult();

        if (empty($data)){
            return $result;
        }

        $pages = ceil($paginator->count() / $limit);

        $result['data'] = $data;
        $result['pages'] = $pages;
        $result['page'] = $page;
        $result['limit'] = $limit;

        return $result;
    }
    
    public function findAgeDesc(int $page, string $value, int $limit = 8): array // recupere les produit par ordre decroissant d'une tranche age donnee
    {
        $limit = abs($limit);

        $result = [];

        $query = $this->createQueryBuilder('p')
        ->andWhere('p.age = :val')
        ->setParameter('val', $value)
        ->orderBy('p.id', 'DESC')
        ->setMaxResults($limit)
        ->setFirstResult(($page * $limit) - $limit);
        
        $paginator = new Paginator($query);

        $data = $paginator->getQuery()->getResult();

        if (empty($data)){
            return $result;
        }

        $pages = ceil($paginator->count() / $limit);

        $result['data'] = $data;
        $result['pages'] = $pages;
        $result['page'] = $page;
        $result['limit'] = $limit;

        return $result;
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
    
    public function findAllDesc(int $page, int $limit = 8): array // recupere les produit par ordre decroissant
    {
        $limit = abs($limit);

        $result = [];

        $query = $this->createQueryBuilder('p')
        ->orderBy('p.id', 'DESC')
        ->setMaxResults($limit)
        ->setFirstResult(($page * $limit) - $limit);
        
        $paginator = new Paginator($query);

        $data = $paginator->getQuery()->getResult();

        if (empty($data)){
            return $result;
        }

        $pages = ceil($paginator->count() / $limit);

        $result['data'] = $data;
        $result['pages'] = $pages;
        $result['page'] = $page;
        $result['limit'] = $limit;

        return $result;
    }
    
    public function findAllCategoryDesc(int $page, string $value, int $limit = 8): array // recupere les produit par ordre decroissant
    {
        $limit = abs($limit);

        $result = [];

        $query = $this->createQueryBuilder('p')
        ->join('p.id_category', 'f')
        ->join('f.category', 'm')
        ->where('m.id = :val')
        ->setParameter('val', $value)
        ->orderBy('p.id', 'DESC')
        ->setMaxResults($limit)
        ->setFirstResult(($page * $limit) - $limit);
        
        $paginator = new Paginator($query);

        $data = $paginator->getQuery()->getResult();

        if (empty($data)){
            return $result;
        }

        $pages = ceil($paginator->count() / $limit);

        $result['data'] = $data;
        $result['pages'] = $pages;
        $result['page'] = $page;
        $result['limit'] = $limit;

        return $result;
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
   
   public function findSearch(int $page, array $data, int $limit):array
   {
        $limit = abs($limit);

        $result = [];

        $query = $this->createQueryBuilder('p')
            ->join('p.id_category', 'c');

        for($i = 0; $i < count($data['age']); $i++){
            $query->andWhere('p.age = :age')
                ->setParameter('age', $data['age'][$i]);
        }

        if (!empty($data['categories'])) {
            $query->andWhere('c.id IN (:categories)')
                ->setParameter('categories', $data['categories']);
        }

        $query->orderBy('p.id', 'DESC')
        ->setMaxResults($limit)
        ->setFirstResult(($page * $limit) - $limit);
        
        $paginator = new Paginator($query);

        $datas = $paginator->getQuery()->getResult();

        if (empty($datas)){
            return $result;
        }

        $pages = ceil($paginator->count() / $limit);

        $result['data'] = $datas;
        $result['pages'] = $pages;
        $result['page'] = $page;
        $result['limit'] = $limit;

        return $result;
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
