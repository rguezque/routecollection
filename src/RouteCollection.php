<?php declare(strict_types = 1);
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2022-2024 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @link      https://github.com/rguezque
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace rguezque\RouteCollection;

use Closure;
use InvalidArgumentException;
use rguezque\RouteCollection\Interfaces\MiddlewareInterface;

/**
 * Routes collector
 * 
 * @method void route(string $http_method, string $route_path, callable $controller) Add a route to the collection
 * @method void routeGroup(string $prefix, Closure $closure) Add a routes group to the collection
 * @method array getRoutes() Return all the registered routes from this instance
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
    private $routes = [];

    /**
     * Middlewares array for all routes
     * 
     * @var MiddlewareInterface[]
     */
    private $middlewares = [];

    /**
     * Routes groups collection
     * 
     * @var array
     */
    private $groups = [];

    /**
     * Initialize the routes collection
     * 
     * @param string $prefix The routes prefix
     */
    public function __construct(string $prefix = '') {
        if('' !== trim($prefix)) {
            $this->prefix = self::strPathFormat($prefix);
        }
    }

    /**
     * Add a route to the collection
     * 
     * @param string $http_method The HTTP method for the route
     * @param string $route_path The route path
     * @param callable $controller The route controller
     * @return Route
     */
    public function route(string $http_method, string $route_path, callable $controller): Route {
        $http_method = strtoupper(trim($http_method));
        $route_path = $this->prefix.self::strPathFormat($route_path);
        
        $route = new Route($http_method, $route_path, $controller);
        // Apply collection middlewares to route if it doesn't have its own
        if (!empty($this->middlewares)) {
            $route->execBefore(...$this->middlewares);
        }
        $this->routes[$http_method][] = $route;

        return $route;
    }

    /**
     * Add a routes group to the collection
     * 
     * @param string $group_prefix The routes prefix
     * @param Closure $closure The function with routes definition
     * @return RouteCollection
     */
    public function routeGroup(string $group_prefix, Closure $closure): RouteCollection {
        $group_prefix = self::strPathFormat($group_prefix);
        $route_collection = new RouteCollection($this->prefix.$group_prefix);
        $this->groups[] = [$closure, $route_collection];
        // Processes the routes in the route group to retrieve the routes registered in the $route_collection object and merge them with the other existing routes.
        //call_user_func($closure, $route_collection);
        // Retrieve the generated routes and merge
        //$this->routes = array_merge_recursive($this->routes, $route_collection->getRoutes());
        return $route_collection;
    }

    /**
     * Resolve the routes groups recursively
     * 
     * @return void
     */
    public function resolveGroups() {
        foreach($this->groups as $group) {
            list($closure, $route_collection) = $group;
            // Inherit parent middlewares
            if (!empty($this->middlewares)) {
                $route_collection->execBefore(...$this->middlewares);
            }
            call_user_func($closure, $route_collection);
            $route_collection->resolveGroups();
            $this->routes = array_merge_recursive($this->routes, $route_collection->getRoutes());
        }
    }

    /**
     * Set middlewares to route collection
     * 
     * @param MiddlewareInterface[] $middlewares Array of middleware instances
     * @return self
     * @throws InvalidArgumentException If middleware is not valid
     */
    public function execBefore(MiddlewareInterface ...$middlewares): self {
        foreach ($middlewares as $middleware) {
            if (!($middleware instanceof MiddlewareInterface)) {
                throw new InvalidArgumentException('Middleware must implement MiddlewareInterface');
            }
        }
        $this->middlewares = array_merge($this->middlewares, $middlewares);
        return $this;
    }

    /**
     * Clear all middlewares from route collection
     * 
     * @return self
     */
    public function clearMiddlewares(): self {
        $this->middlewares = [];
        return $this;
    }

    /**
     * Return all the middlewares of route collection
     * 
     * @return MiddlewareInterface[]
     */
    public function getActionsBefore(): array {
        return $this->middlewares;
    }

    /**
     * Return all the registered routes from this instance
     * 
     * @return array
     */
    public function getRoutes(): array {
        return $this->routes;
    }

    /**
     * Convert a route path to right format
     * 
     * @param string $path The route path
     * @return string
     */
    public static function strPathFormat(string $path): string {
        return '/'.trim($path, '/\\ ');
    }
    
}

?>