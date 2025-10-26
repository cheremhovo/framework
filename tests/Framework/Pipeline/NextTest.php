<?php

namespace Framework\Pipeline;

use Cheremhovo1990\Framework\Pipeline\Next;
use Laminas\Diactoros\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class NextTest extends TestCase
{
    public function testEmptyQueue()
    {
        $expect = 'empty';
        $next = new Next(new \SplQueue(), function () use ($expect) {
            return $expect;
        });
        $response = $next(new ServerRequest());
        $this->assertEquals($expect, $response);
    }

    public function testQueue()
    {
        $queue = new \SplQueue();
        $queue->enqueue(function (ServerRequestInterface $request, $next) {
            $response = $next($request);
            return $response * 3;
        });
        $queue->enqueue(Middleware::class);
        $next = new Next($queue, function (){
            return 5;
        });
        $response = $next(new ServerRequest());
        $this->assertEquals(9, $response);
    }
}

class Middleware
{
    public function __invoke(ServerRequestInterface $request, $next)
    {
        $response = $next($request);
        return $response - 2;
    }
}