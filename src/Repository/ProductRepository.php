<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;
use App\Entity\Product;

class ProductRepository extends EntityRepository
{
    public function getTotalPriceByIds(array $ids): ?float
    {
        $qb = $this
            ->getEntityManager()
            ->createQueryBuilder()
        ;

        return $qb
            ->select('SUM(p.price)')
            ->from(Product::class, 'p')
            ->setParameter('ids', $ids)
            ->where($qb->expr()->in('p.id', ':ids'))
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }
}
