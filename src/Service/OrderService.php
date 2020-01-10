<?php

namespace App\Service;

use App\Entity\Order;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

final class OrderService extends ObjectService
{
    protected EntityRepository $orderRepository;

    public function __construct()
    {
        $this->orderRepository = $this->getEntityManager()->getRepository(Order::class);
    }

    public function addOrder(Request $request): array
    {
        $result = $this->validateCreate($request);
        if (false === $result['valid']) {
            return $result;
        }

        $order = (new Order())
            ->setPrice($result['price'])
            ->setStatus(Order::STATUS_CREATED)
        ;

        $entityManager = $this->getEntityManager();
        $entityManager->persist($order);
        $entityManager->flush();

        return [
            'body' => (array) $order,
            'status' => Response::HTTP_CREATED,
        ];
    }

    public function chargeOrder(Request $request): array
    {
        $result = $this->validateCharge($request);
        if (false === $result['valid']) {
            return $result;
        }
        $order = $result['order'];

        $order->setStatus(Order::STATUS_CHARGED);

        $entityManager = $this->getEntityManager();
        $entityManager->persist($order);
        $entityManager->flush();

        return [
            'body' => (array) $order,
            'status' => null,
        ];
    }

    public function getObject(int $id): ?Order
    {
        /** @var Order|null $object */
        $object = $this->orderRepository->find($id);

        return $object;
    }

    public function validateCreate(Request $request): array
    {
        $productIds = $request->get('productIds');

        $result = [
            'valid' => false,
            'body' => '',
            'status' => null,
            'price' => 0,
        ];

        if (null === $productIds) {
            $result['body'] = 'productIds does not exist!';
            $result['status'] = Response::HTTP_NOT_FOUND;

            return $result;
        }
        $productService = new ProductService();
        $result['price'] = $productService->getTotalPrice($productIds);
        if ($result['price'] <= 0) {
            $result['body'] = 'No one product is selected.';

            return $result;
        }

        $result['valid'] = true;

        return $result;
    }

    public function validateCharge(Request $request): array
    {
        $id = (int) $request->get('id', 0);
        $price = (float) $request->get('price', 0.0);

        $result = [
            'valid' => false,
            'body' => '',
            'status' => null,
        ];
        $result['order'] = $this->getObject($id);
        if (null === $result['order']) {
            $result['body'] = "Order #$id does not exist!";
            $result['status'] = Response::HTTP_NOT_FOUND;

            return $result;
        }

        if ($result['order']->isCharged()) {
            $result['body'] = "Order #$id is already charged!";

            return $result;
        }

        if ($result['order']->isNotEqualPrice($price)) {
            $result['body'] = 'Price is not equal order price!';

            return $result;
        }

        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'http://ya.ru');
        if (Response::HTTP_OK !== $response->getStatusCode()) {
            $result['body'] = 'Service ya.ru is not available.';

            return $result;
        }

        $result['valid'] = true;

        return $result;
    }
}
