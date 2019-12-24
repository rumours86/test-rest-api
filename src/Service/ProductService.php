<?php

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;

final class ProductService extends ObjectService
{
    protected ProductRepository $productRepository;

    public function __construct()
    {
        $this->productRepository = $this->getEntityManager()->getRepository(Product::class);
    }

    public function getObject(int $id): ?Product
    {
        /** @var Product|null $object */
        $object = $this->productRepository->find($id);

        return $object;
    }

    public function findAll(): ?array
    {
        return $this->productRepository->findAll();
    }

    public function generateBatchProducts(int $count = 20): array
    {
        $entityManager = $this->getEntityManager();
        $objectList = [];
        for ($i = 0; $i < $count; ++$i) {
            $object =
                (new Product())
                    ->setTitle(sha1(mt_rand()))
                    ->setPrice(random_int(1, 1000))
            ;

            $entityManager->persist($object);
            $objectList[] = (array) $object;
        }
        $entityManager->flush();
        $entityManager->clear();

        return $objectList;
    }

    public function getTotalPrice(array $productIds): float
    {
        return $this->productRepository->getTotalPriceByIds($productIds);
    }
}
