<?php


namespace App\System;

use Psr\Container\ContainerInterface;

class Container implements ContainerInterface
{
    private array $container;

    /**
     * @inheritDoc
     */
    public function get($id)
    {
        $id = (string) $id;

        return $this->container[$id] ?? null;
    }

    /**
     * @inheritDoc
     */
    public function has($id): bool
    {
        $id = (string) $id;

        return isset($this->container[$id]);
    }

    public function set(string $id, ?object $service): void
    {
        $this->container[$id] = $service;
    }
}
