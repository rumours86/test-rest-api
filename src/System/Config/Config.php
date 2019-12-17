<?php

namespace App\System\Config;

use Symfony\Component\Config\FileLocator;

class Config implements ConfigInterface
{
    private array $config = [];
    private YamlConfigLoader $loader;
    private FileLocator $locator;

    public function __construct()
    {
        $this->locator = new FileLocator(PROJECT_DIR . '/config');
        $this->loader = new YamlConfigLoader($this->locator);
    }

    public function addConfig(string $file): void
    {
        $configs = $this->loader->load($this->locator->locate($file));
        if (empty($configs)) {
            return;
        }
        foreach ($configs as $key => $value) {
            $this->config[$key] = $value;
        }
    }

    public function get(string $key)
    {
        if (!$key || !isset($this->config[$key])) {
            return null;
        }

        return $this->config[$key];
    }
}