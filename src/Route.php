<?php declare(strict_types = 1);
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2022-2024 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @link      https://github.com/rguezque
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace rguezque\RouteCollection;

use rguezque\RouteCollection\Interfaces\MiddlewareInterface;

/**
 * Represents a route
 * 
 * @method string getRoutePath() Retrieve the route path
 * @method string getRouteMethod() Retrieve the route method
 * @method callable getRouteController() Retrieve the route controller
 */
class Route {
    /**
     * The HTTP method for the route
     * 
     * @var string
     */
    private $http_method;

    /**
     * The route path
     * 
     * @var string
     */
    private $route_path;

    /**
     * The route controller
     * 
     * @var callable
     */
    private $controller;

    /**
     * Middlewares array for route
     * 
     * @var MiddlewareInterface[]
     */
    private $middlewares = [];

    /**
     * Initialize a route
     * 
     * @param string $http_method The HTTP method for the route
     * @param string $route_path The route path
     * @param callable $controller The route controller
     */
    public function __construct(string $http_method, string $route_path, callable $controller) {
        $this->http_method = $http_method;
        $this->route_path = $route_path;
        $this->controller = $controller;
    }

    /**
     * Retrieve the route path
     * 
     * @return string
     */
    public function getRoutePath(): string {
        return $this->route_path;
    }

    /**
     * Retrieve the route method
     * 
     * @return string
     */
    public function getRouteMethod(): string {
        return $this->http_method;
    }

    /**
     * retrieve the route controller
     * 
     * @return callable
     */
    public function getRouteController(): callable {
        return $this->controller;
    }

    /**
     * Set middlewares to route
     * 
     * @param MiddlewaresInterface[]
     * @return void
     */
    public function execBefore(MiddlewareInterface ...$middlewares): void {
        $this->middlewares = $middlewares;
    }

    /**
     * Return all the middlewares of route
     * 
     * @return MiddlewareInterface[]
     */
    public function getActionsBefore(): array {
        return $this->middlewares;
    }
}

?>