<?php declare(strict_types = 1);
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2022-2024 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @link      https://github.com/rguezque
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace rguezque\RouteCollection;

use Closure;

/**
 * Routes collector
 * 
 * @method void route(string $http_method, string $route_path, callable $controller) Add a route to the collection
 * @method void routeGroup(string $prefix, Closure $closure) Add a routes group to the collection
 */
class RouteCollection {

    /**
     * Routes prefix
     * 
     * @var string
     */
    private $prefix = '';

    /**
     * Routes collection
     * 
     * @var Route[]
     */
    public $routes = [];

    /**
     * Initialize the routes collection
     * 
     * @param string $prefix The routes prefix
     */
    public function __construct(string $prefix = '') {
        if('' !== $prefix) {
            $this->prefix = '/'.trim($prefix, '/\\');
        }
    }

    /**
     * Add a route to the collection
     * 
     * @param string $http_method The HTTP method for the route
     * @param string $route_path The route path
     * @param callable $controller The route controller
     * @return void
     */
    public function route(string $http_method, string $route_path, callable $controller): void {
        $http_method = strtoupper(trim($http_method));
        $route_path = $this->prefix.'/'.trim($route_path, '/\\');
        
        $route = new Route($http_method, $route_path, $controller);
        $this->routes[] = $route;
    }

    /**
     * Add a routes group to the collection
     * 
     * @param string $group_prefix The routes prefix
     * @param Closure $closure The function with routes definition
     * @return void
     */
    public function routeGroup(string $group_prefix, Closure $closure): void{
        $group_prefix = '/'.trim($group_prefix, '/\\');
        $route_collection = new RouteCollection($this->prefix.$group_prefix);
        call_user_func($closure, $route_collection);
        $this->routes = array_merge($this->routes, $route_collection->routes);
    }
    
}



?>