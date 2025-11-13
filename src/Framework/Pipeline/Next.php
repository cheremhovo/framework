<?php

namespace Cheremhovo1990\Framework\Pipeline;

use Cheremhovo1990\Framework\Resolver;
use Cheremhovo1990\Framework\RequestHandlerWrapper;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Next
{
    protected \SplQueue $queue;
    private RequestHandlerInterface $controller;
    private ContainerInterface $container;

    public function __construct(
        \SplQueue $queue,
        RequestHandlerInterface $controller,
        ContainerInterface $container,
    )
    {
        $this->queue = $queue;
        $this->controller = $controller;
        $this->container = $container;
    }

    public function __invoke(ServerRequestInterface $request)
    {
        if ($this->queue->isEmpty()) {
            return $this->controller->handle($request);
        }
        $middleware = $this->getMiddleware();
        return $middleware->process($request, new RequestHandlerWrapper($this));
    }

    protected function getMiddleware(): MiddlewareInterface
    {
        $middleware = $this->queue->dequeue();
        if (is_string($middleware)) {
            return $this->container->get($middleware);
        } else {
            return $middleware;
        }
    }
}