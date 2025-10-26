<?php

namespace Framework;

use Cheremhovo1990\Framework\Resolver;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ResolverTest extends TestCase
{
    #[DataProvider('provider')]
    public function testResolve($handle, $expect)
    {
        $resolver = new Resolver();
        $result = $resolver->resolve($handle);

        $this->assertEquals($expect, $result());
    }

    public static function provider()
    {
        return [
            'Callable Callback' => [
                function () {
                    return 'callback';
                },
                'callback'
            ],
            'Callable Class' => [Controller::class, 'controller'],
        ];
    }
}

class Controller
{
    public function __invoke()
    {
        return 'controller';
    }
}