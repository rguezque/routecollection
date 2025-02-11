<?php declare(strict_types = 1);

namespace rguezque\RouteCollection\Interfaces;

use rguezque\RouteCollection\HttpResponse;
use rguezque\RouteCollection\ServerRequest;

interface MiddlewareInterface {
    public function handle(ServerRequest $request, callable $next);
}