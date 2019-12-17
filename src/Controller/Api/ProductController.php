<?php

namespace App\Controller\Api;

use App\Service\ProductService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    private ProductService $productService;

    public function __construct()
    {
        $this->productService = new ProductService();
    }

    public function postGenerate(Request $request): Response
    {
        $count = (int) $request->get('count', 20);
        $objectList = $this->productService->generateBatchProducts($count);

        return new JsonResponse($objectList, Response::HTTP_CREATED);
    }

    public function getProductList(): Response
    {
        $objectList = $this->productService->findAll();

        return new JsonResponse($objectList);
    }
}