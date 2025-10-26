<?php

namespace App\Controller;

use Psr\Http\Message\ServerRequestInterface;

class DefaultController
{
    public function __invoke(ServerRequestInterface $request)
    {
        return 'Hello Word!!!';
    }
}