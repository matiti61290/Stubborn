<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    //Query to find highlighted products
    public function findAllHighlightedProducts(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.highlighted = :highlighted')
            ->setParameter('highlighted', true)
            ->getQuery()
            ->getResult();
    }

    //Query to get all product
    public function findAllProducts(): array
    {
        return $this->createQueryBuilder('p')
            ->getQuery()
            ->getResult();
    }

    //Query to find product by price interval
    public function findByPriceInterval(?string $priceRange): array
    {
        $qb = $this->createQueryBuilder('p');

        if ($priceRange) {
            list($min, $max) = explode('-', $priceRange);
            $qb->andWhere('p.price BETWEEN :min AND :max')
            ->setParameter('min', $min)
            ->setParameter('max', $max);
        }

        return $qb->getQuery()->getResult();
    }
}
