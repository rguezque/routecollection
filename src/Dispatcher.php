<?php declare(strict_types = 1);
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2022-2024 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @link      https://github.com/rguezque
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace rguezque\RouteCollection;

use Closure;
use UnexpectedValueException;
use RuntimeException;

/**
 * Routes dispatcher
 * 
 * @method array match(string $request_uri, string $request_method) Run the router and match the request URI with the routes
 * @method void dispatch(string $request_uri, string $request_method) Dispatch the router and route controller
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
        $this->routes = $route_collection->routes;
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
     * Dispatch the router and route controller
     * 
     * @param string $request_uri The request URI
     * @param string $request_method The request HTTP method
     * @throws UnexpectedValueException
     * @throws RuntimeException
     * @return void
     */
    public function dispatch(string $request_uri, string $request_method): void {
        $router_params = $this->match($request_uri, $request_method);

        switch($router_params['status_code']) {
            case Dispatcher::FOUND: 
                $result = call_user_func($router_params['route_controller'], $router_params['route_params']);
                
                if(is_null($result)) {
                    $buffer = ob_get_clean();
                    throw new UnexpectedValueException(sprintf('The route "%s" with %s method must return a result.', $router_params['route_path'], $router_params['route_method']));
                }

                //http_response_code(200);
                echo $result;
                break;
        
            case Dispatcher::NOT_FOUND:
                throw new RuntimeException('The request URI do not match any route.');
                break;
        }
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