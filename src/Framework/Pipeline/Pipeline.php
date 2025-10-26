<?php

namespace Cheremhovo1990\Framework\Pipeline;

use Cheremhovo1990\Framework\CallableMiddlewareWrapper;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Pipeline
{
    public function __construct()
    {
        $this->queue = new \SplQueue();
    }

    public function pipe($middleware): void
    {
        if (is_callable($middleware)) {
            $middleware = new CallableMiddlewareWrapper($middleware);
        }
        $this->queue->enqueue($middleware);
    }

    public function __invoke(ServerRequestInterface $request, RequestHandlerInterface $controller)
    {
        $next = new Next(clone $this->queue, $controller);
        return $next($request);
    }
}