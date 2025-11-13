<?php

/** @var Cheremhovo1990\Framework\Router\RouteCollection $routes  */

use App\Controller\DefaultController;

$routes->get('default', '/', DefaultController::class);

$routes->get('about', '/about', function () {
    $request = ServerRequestFactory::fromGlobals();
    $name = $request->getQueryParams()['name'] ?: 'Guest';
    return 'hello ' . $name . '!';
});