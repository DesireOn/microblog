<?php

namespace App\Command;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

interface CommandInterface
{
    public function execute(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface;
}