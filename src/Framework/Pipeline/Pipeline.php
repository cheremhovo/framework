<?php

namespace Cheremhovo1990\Framework\Pipeline;

use Cheremhovo1990\Framework\CallableMiddlewareWrapper;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Pipeline
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->queue = new \SplQueue();
        $this->container = $container;
    }

    public function pipe(string|callable $middleware): void
    {
        if (is_callable($middleware)) {
            $middleware = new CallableMiddlewareWrapper($middleware);
        }
        $this->queue->enqueue($middleware);
    }

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $controller)
    {
        $next = new Next(
            clone $this->queue,
            $controller,
            $this->container
        );
        return $next($request);
    }
}