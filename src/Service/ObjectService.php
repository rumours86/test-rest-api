<?php

namespace App\Service;

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
        global $app;
        return $app->get('doctrine');
    }
}
