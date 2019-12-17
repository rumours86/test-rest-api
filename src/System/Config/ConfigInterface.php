<?php
namespace App\System\Config;

interface ConfigInterface
{
    public function addConfig(string $file): void;
    public function get(string $key);
}
