<?php

namespace App\Service;

use App\Entity\Order;
use Doctrine\ORM\EntityRepository;

final class OrderService extends ObjectService
{
    protected EntityRepository $objectRepository;

    public function __construct()
    {
        $this->objectRepository = $this->getRepository(Order::class);
    }

    public function addOrder(float $price): Order
    {
        $order = (new Order())
            ->setPrice($price)
            ->setStatus(Order::STATUS_CREATED)
        ;

        $entityManager = $this->getEntityManager();
        $entityManager->persist($order);
        $entityManager->flush();

        return $order;
    }

    public function getObject(int $id): ?Order
    {
        /** @var Order|null $object */
        $object = $this->objectRepository->find($id);

        return $object;
    }

    public function chargeOrder(Order $order): Order
    {
        $order->setStatus(Order::STATUS_CHARGED);

        $entityManager = $this->getEntityManager();
        $entityManager->persist($order);
        $entityManager->flush();

        return $order;
    }
}
