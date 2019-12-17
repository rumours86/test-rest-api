<?php

namespace App\Controller\Api;

use App\Service\OrderService;
use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends AbstractController
{
    private OrderService $orderService;
    private ProductService $productService;

    public function __construct()
    {
        $this->orderService = new OrderService();
        $this->productService = new ProductService();
    }

    public function postCreate(Request $request): Response
    {
        $productIds = $request->get('productIds');
        if (null === $productIds) {
            return new JsonResponse('productIds does not exist!', Response::HTTP_NOT_FOUND);
        }
        $price = $this->productService->getTotalPrice($productIds);
        if ($price <= 0) {
            return new JsonResponse('No one product is selected.');
        }

        $order = $this->orderService->addOrder($price);

        return new JsonResponse((array) $order, Response::HTTP_CREATED);
    }

    public function putCharge(Request $request): Response
    {
        $id = (int) $request->get('id', 0);
        $price = (float) $request->get('price', 0.0);

        $order = $this->orderService->getObject($id);
        if (null === $order) {
            return new JsonResponse("Order #$id does not exist!", Response::HTTP_NOT_FOUND);
        }

        if ($order->isCharged()) {
            return new JsonResponse("Order #$id is already charged!");
        }

        if ($order->isNotEqualPrice($price)) {
            return new JsonResponse('Price is not equal order price!');
        }

        $httpClient = HttpClient::create();
        $response = $httpClient->request('GET', 'http://ya.ru');
        if (Response::HTTP_OK !== $response->getStatusCode()) {
            return new JsonResponse('Service ya.ru is not available.');
        }

        $order = $this->orderService->chargeOrder($order);

        return new JsonResponse((array) $order);
    }
}
