<?php

use Cheremhovo1990\Framework\Container\Container;
use Cheremhovo1990\Framework\Pipeline\Pipeline;
use Cheremhovo1990\Framework\RequestHandlerWrapper;
use Cheremhovo1990\Framework\Router\RouteCollection;
use Cheremhovo1990\Framework\Router\Router;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

require __DIR__ . '/../vendor/autoload.php';

### Initialization

$container = new Container();
$routes = new RouteCollection();

require __DIR__ . '/../config/routes.php';

$router = new Router($routes);
$pipeline = new Pipeline($container);

require __DIR__ . '/../config/pipeline.php';

### Running
$request = ServerRequestFactory::fromGlobals();

/** @var callable $controller */
$controller = $router->match($request);
$controller = function (ServerRequestInterface $request) use ($controller, $container): ResponseInterface {
    if (is_string($controller)) {
        $controller = $container->get($controller);
    }
    $response = ($controller)($request);
    if (!$response instanceof ResponseInterface) {
        if (is_string($response)) {
            $response = new HtmlResponse($response);
        }
    }
    return $response;
};
$response = $pipeline($request, new RequestHandlerWrapper($controller));

### Postprocessing

$response = $response->withHeader('X-ID', 'Mini');

### Sending

(new SapiEmitter())->emit($response);