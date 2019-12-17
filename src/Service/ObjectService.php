<?php

namespace App\Service;

use App\Kernel;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;

class ObjectService
{
    public function getRepository(string $class): EntityRepository
    {
        return $this->getEntityManager()->getRepository($class);
    }

    protected function getEntityManager(): EntityManager
    {
        $app = Kernel::getInstance();

        return $app->get('doctrine');
    }
}
