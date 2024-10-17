<?php declare(strict_types = 1);
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2022-2024 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @link      https://github.com/rguezque
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace rguezque\RouteCollection;

use ErrorException;
use RuntimeException;
use UnexpectedValueException;

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
     * CORS configuration
     * 
     * @var CorsConfig
     */
    private $cors;

    /**
     * Initialize dispatcher
     * 
     * @param Routecollection $route_collection The routes collection
     * @param CorsConfig $cors The CORS configuration to resolve
     */
    public function __construct(RouteCollection $route_collection, ?CorsConfig $cors = null) {
        $this->routes = $route_collection->getRoutes();
        if(null !== $cors) {
            $this->cors = $cors;
        }
    }

    /**
     * Handle the request URi and HTTP request method, comparing them with each defined route until the first match is found. 
     * Then dispatch the route controller and route params; additionally data such as status code of routing, route URI and route HTTP method.
     * 
     * @param ServerRequest $request A ServerRequest object with actual request
     * @return array
     */
    public function match(ServerRequest $request): array {
        if(null !== $this->cors) {
            call_user_func($this->cors, $request);
        }

        $request_uri = $request->server->get('REQUEST_URI');
        $request_method = $request->server->get('REQUEST_METHOD');

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
     * Provides a way to process requests and routes. If the route does not exist it throws a RouteNotFoundException and if the route's controller does not return an HttpResponse it throws an UnexpectedValueException
     * 
     * @param ServerRequest $request A ServerRequest object with request data
     * @return HttpResponse
     * @throws UnexpectedValueException Whet the controller don't return a HttpResponse object
     * @throws RuntimeException When tue route don't exist
     */
    public function dispatch(ServerRequest $request): HttpResponse {
        $router_params = $this->match($request);

        switch($router_params['status_code']) {
            case Dispatcher::FOUND: 
                $request = new ServerRequest;
                $request->withParams($router_params['route_params']);
                $result = call_user_func($router_params['route_controller'], $request);
                ob_get_clean();
                
                if(is_null($result) || !$result instanceof HttpResponse) {
                    $message = sprintf('The route "%s" with %s method must return an HttpResponse, catched %s.', $router_params['route_path'], $router_params['route_method'], gettype($result));
                    throw new UnexpectedValueException($message); // error 406
                    break;
                }
        
                return $result;
                break;
        
            case Dispatcher::NOT_FOUND:
                $message = sprintf('The request URI "%s" with %s method do not match any route.', $router_params['request_uri'], $router_params['request_method']);
                throw new RuntimeException($message); // error 404
                break;
        
            default:
                throw new ErrorException('Something went wrong!'); // error 500
        }
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