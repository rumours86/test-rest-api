<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class AbstractBaseApiController extends AbstractController
{
    public function baseResponse(array $result): Response
    {
        return new JsonResponse($result['body'], $result['message']);
    }
}
