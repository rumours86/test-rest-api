<?php

namespace App;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Exception;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentResolver;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Loader\YamlFileLoader;
use Symfony\Component\Routing\Matcher\UrlMatcher;
use Symfony\Component\Routing\RequestContext;
use Symfony\Component\Routing\RouteCollection;

class Kernel
{
    public static ?self $instance = null;
    private Request $request;
    private RouteCollection $routes;
    private RequestContext $requestContext;
    private array $container = [];

    public function __construct()
    {
        $this->request = Request::createFromGlobals();

        $this->requestContext = new RequestContext();
        $this->requestContext->fromRequest($this->request);

        $fileLocator = new FileLocator([PROJECT_DIR . '/config']);
        $loader = new YamlFileLoader($fileLocator);
        $this->routes = $loader->load('routes.yaml');

        $doctrineConfig = Setup::createAnnotationMetadataConfiguration([PROJECT_DIR . '/src/Entity'], true, null, null, false);
        $conf = [
            'host' => getenv('DB_HOST'),
            'port' => getenv('DB_PORT'),
            'driver' => getenv('DB_DRIVER'),
            'charset' => getenv('DB_CHARSET'),
            'dbname' => getenv('DB_NAME'),
            'user' => getenv('DB_USER'),
            'password' => getenv('DB_PASS'),
        ];

        $doctrine = EntityManager::create($conf, $doctrineConfig);

        $this->add('doctrine', $doctrine);
    }

    public static function getInstance(): self
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    public function run(): void
    {
        $response = null;
        $matcher = new UrlMatcher($this->routes, $this->requestContext);

        $this->request->attributes->add($matcher->match($this->request->getPathInfo()));

        // load controller
        if (false === $controller = (new ControllerResolver())->getController($this->request)) {
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

    public function add(string $key, $object): void
    {
        $this->container[$key] = $object;
    }

    public function get(string $key)
    {
        return $this->container[$key] ?? null;
    }
}
