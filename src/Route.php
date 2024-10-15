<?php declare(strict_types = 1);
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2022-2024 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @link      https://github.com/rguezque
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace rguezque\RouteCollection;

/**
 * Represents a route
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

    public function getPath() {
        return $this->route_path;
    }

    public function getMethod() {
        return $this->http_method;
    }

    public function getController() {
        return $this->controller;
    }
}

?>