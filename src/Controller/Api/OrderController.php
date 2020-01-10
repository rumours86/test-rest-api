<?php

namespace App\Controller\Api;

use App\Service\OrderService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends AbstractBaseApiController
{
    private OrderService $orderService;

    public function __construct()
    {
        $this->orderService = new OrderService();
    }

    public function postCreate(Request $request): Response
    {
        return $this->baseResponse($this->orderService->addOrder($request));
    }

    public function putCharge(Request $request): Response
    {
        return $this->baseResponse($this->orderService->chargeOrder($request));
    }
}
