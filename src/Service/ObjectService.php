<?php

namespace App\Service;

use App\Kernel;
use Doctrine\ORM\EntityManager;

class ObjectService
{
    protected function getEntityManager(): EntityManager
    {
        $app = Kernel::getInstance();

        return $app->get('doctrine');
    }
}
