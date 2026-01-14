<?php

declare(strict_types=1);

namespace Cheremhovo1990\Framework\Router;

class RouteCollection
{
    /** @var array|RouteInterface[] */
    private array $routes = [];

    public function addRoute(RouteInterface $route)
    {
        $this->routes[] = $route;
    }

    public function any(string $name, string $pattern, $controller, array $methods = [], array $options = [], array $middlewares = [])
    {
        $this->addRoute(new Route($name, $pattern, $controller, $methods, $options, $middlewares));
    }

    public function get(string $name, string $pattern, $controller, array $options = [], array $middlewares = [])
    {
        $this->addRoute(new Route($name, $pattern, $controller, ['GET'], $options, $middlewares));
    }

    public function post(string $name, string $pattern, $controller, array $options = [], array $middlewares = [])
    {
        $this->addRoute(new Route($name, $pattern, $controller, ['POST'], $options, $middlewares));
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}