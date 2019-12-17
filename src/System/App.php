<?php

namespace App\System;

use App\System\Config\Config;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Exception;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

class App
{
    private Request $request;
    private RouteCollection $routes;
    private RequestContext $requestContext;
    private ContainerInterface $container;

    public function __construct()
    {
        $this->container = new Container();
        $this->request = Request::createFromGlobals();

        $this->requestContext = new RequestContext();
        $this->requestContext->fromRequest($this->request);

        $fileLocator = new FileLocator([PROJECT_DIR . '/config']);
        $loader = new YamlFileLoader($fileLocator);
        $this->routes = $loader->load('routes.yaml');

        $config = new Config();
        $config->addConfig('doctrine.yaml');

        $doctrineConfig = Setup::createAnnotationMetadataConfiguration([PROJECT_DIR . '/src/Entity'], true);
        $doctrine = EntityManager::create($config->get('doctrine'), $doctrineConfig);

        $this->add('doctrine', $doctrine);
    }

    public function getContainer(): ContainerInterface
    {
        return $this->container;
    }

    public function run(): void
    {
        $response = null;
        $matcher = new UrlMatcher($this->routes, $this->requestContext);

        $this->request->attributes->add($matcher->match($this->request->getPathInfo()));

        // load controller
        if (false === $controller =  (new ControllerResolver())->getController($this->request)) {
            throw new NotFoundHttpException(sprintf('Unable to find the controller for path "%s". The route is wrongly configured.', $this->request->getPathInfo()));
        }

        try {
            $arguments = (new ArgumentResolver())->getArguments($this->request, $controller);

            $response = call_user_func_array($controller, $arguments);
        } catch (Exception $exception) {
            var_export($exception->getTrace());
            exit($exception->getMessage());
        }

        if (null === $response) {
            exit('Response is null');
        }

        $response->send();
    }

    public function add($key, $object): void
    {
        $this->container->set($key, $object);
    }

    public function get($key)
    {
        return $this->container->get($key);
    }
}
