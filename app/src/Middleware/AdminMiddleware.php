<?php

namespace App\Middleware;

use App\Service\Auth;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * A middleware class that is being activated when user visits "/admin/*".
 * It redirects user to homepage whenever he/she is not logged in.
 */
class AdminMiddleware
{
    private Auth $auth;

    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * @param ServerRequestInterface $request
     * @param ResponseInterface $response
     * @param callable $next
     * @return ResponseInterface
     */
    public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
    {
        if ($this->auth->isLogged() === false) {
            return $response->withRedirect('/admin/login');
        }

        return $next($request, $response);
    }
}