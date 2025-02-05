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

    public function findAllHighlightedProducts(): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.highlighted = :highlighted')
            ->setParameter('highlighted', true) // true au lieu de 1 pour plus de clarté
            ->getQuery()
            ->getResult();
    }

    public function findAllProducts(): array
    {
        return $this->createQueryBuilder('p')
            ->getQuery()
            ->getResult();
    }
}
