<?php

namespace App\Controller\Api;

use App\Service\OrderService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends AbstractController
{
    private OrderService $orderService;

    public function __construct()
    {
        $this->orderService = new OrderService();
    }

    public function postCreate(Request $request): Response
    {
        $result = $this->orderService->validateCreate($request);
        if (false === $result['valid']) {
            return new JsonResponse($result['message'], $result['status']);
        }

        $order = $this->orderService->addOrder($result['price']);

        return new JsonResponse((array) $order, Response::HTTP_CREATED);
    }

    public function putCharge(Request $request): Response
    {
        $result = $this->orderService->validateCharge($request);
        if (false === $result['valid']) {
            return new JsonResponse($result['message'], $result['status']);
        }
        $order = $this->orderService->chargeOrder($result['order']);

        return new JsonResponse((array) $order);
    }
}
