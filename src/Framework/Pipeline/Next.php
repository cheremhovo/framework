<?php

namespace Cheremhovo1990\Framework\Pipeline;

use Cheremhovo1990\Framework\Resolver;
use Cheremhovo1990\Framework\RequestHandlerWrapper;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Next
{
    protected \SplQueue $queue;
    private RequestHandlerInterface $controller;

    private Resolver $resolver;

    public function __construct(\SplQueue $queue, RequestHandlerInterface $controller)
    {
        $this->queue = $queue;
        $this->controller = $controller;
        $this->resolver = new Resolver();
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
        return $this->resolver->resolve($this->queue->dequeue());
    }
}