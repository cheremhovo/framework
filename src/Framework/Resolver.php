<?php

namespace Cheremhovo1990\Framework;

class Resolver
{
    public function resolve($handler)
    {
        if (is_string($handler)) {
            return new $handler();
        }
        return $handler;
    }
}