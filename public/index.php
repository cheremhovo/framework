<?php

use App\Controller\DefaultController;
use Cheremhovo1990\Framework\Pipeline\Pipeline;
use Cheremhovo1990\Framework\RequestHandlerWrapper;
use Cheremhovo1990\Framework\Resolver;
use Cheremhovo1990\Framework\Router\RouteCollection;
use Cheremhovo1990\Framework\Router\Router;
use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\HttpHandlerRunner\Emitter\SapiEmitter;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

require __DIR__ . '/../vendor/autoload.php';

### Initialization

$routes = new RouteCollection();

$routes->get('default', '/', DefaultController::class);

$routes->get('about', '/about', function () {
    $request = ServerRequestFactory::fromGlobals();
    $name = $request->getQueryParams()['name'] ?: 'Guest';
    return 'hello ' . $name . '!';
});

$router = new Router($routes);
$resolver = new Resolver();
$pipeline = new Pipeline();

$pipeline->pipe(\App\Middleware\ProfileMiddleware::class);
$pipeline->pipe(function (ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface {
    $response = $handler->handle($request);
    return $response->withHeader('X-Developer-email', 'cheremhovo1990@yandex.ru');
});

### Running
$request = ServerRequestFactory::fromGlobals();

/** @var callable $controller */
$controller = $router->match($request);
$controller = function (ServerRequestInterface $request) use ($controller, $resolver): ResponseInterface {
    $response = $resolver->resolve($controller)($request);
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