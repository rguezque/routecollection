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
 * @method array match(string $request_uri, string $request_method) Run the router and handle the request URI and request http method
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
     * Handle the request URi and HTTP request method, comparing them with each defined route until the first match is found. 
     * Then dispatch the route controller and route params; additionally data such as status code of routing, route URI and route HTTP method.
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
            $pattern = $this->getRegexPattern($route->getPath());

            if ($route->getMethod() === $request_method && preg_match($pattern, $request_uri, $params)) {
                array_shift($params);

                return [
                    'status_code' => Dispatcher::FOUND,
                    'route_path' => $route->getPath(),
                    'route_method' => $route->getMethod(),
                    'route_controller' => $route->getController(),
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
    private function getRegexPattern(string $path): string {
        $path = str_replace('/', '\/', '/'.trim($path, '/\\'));
        $path = preg_replace('#{(\w+)}#', '(?<$1>\w+)', $path); // Replace wildcards

        return '#^' . $path . '$#i';
    }
}

?>