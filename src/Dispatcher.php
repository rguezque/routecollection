<?php declare(strict_types = 1);
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2022-2024 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @link      https://github.com/rguezque
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace rguezque\RouteCollection;

/**
 * Routes dispatcher
 * 
 * @method array match(string $request_uri, string $request_method) Run the router and match the request URI with the routes
 */
class Dispatcher {

    /**
     * Flag for route not found
     * 
     * @var int
     */
    const NOT_FOUND = 0;

    /**
     * Flag for route found
     * 
     * @var int
     */
    const FOUND = 1;

    /**
     * Routes collection
     * 
     * @var Route[]
     */
    private $routes = [];

    /**
     * Initialize dispatcher
     * 
     * @param Routecollection $route_collection The routes collection
     */
    public function __construct(RouteCollection $route_collection) {
        $this->routes = $route_collection->getRoutes();
    }

    /**
     * Run the router and match the request URI with the routes
     * 
     * @param string $request_uri The request URI
     * @param string $request_method The request HTTP method
     * @return array
     */
    public function match(string $request_uri, string $request_method): array {
        $request_uri = rawurldecode(parse_url($request_uri, PHP_URL_PATH));

        if('/' !== $request_uri) {
            $request_uri = rtrim($request_uri, '/\\');
        }

        foreach ($this->routes as $route) {
            $pattern = $this->getRegex($route->route_path);

            if ($route->http_method === $request_method && preg_match($pattern, $request_uri, $params)) {
                array_shift($params);

                return [
                    'status_code' => Dispatcher::FOUND,
                    'route_path' => $route->route_path,
                    'route_method' => $route->http_method,
                    'route_controller' => $route->controller,
                    'route_params' => $params
                ];
            }
        }

        return [
            'status_code' => Dispatcher::NOT_FOUND,
            'request_method' => $request_method,
            'request_uri' => $request_uri
        ];
    }

    /**
     * Return a regex pattern for route path
     * 
     * @param string $path The route path
     * @return string
     */
    private function getRegex(string $path): string {
        $path = str_replace('/', '\/', '/'.trim($path, '/\\'));
        $path = preg_replace('#{(\w+)}#', '(?<$1>\w+)', $path); // Replace wildcards

        return '#^' . $path . '$#i';
    }
}

?>