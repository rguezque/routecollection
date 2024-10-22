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
 * @method HttpResponse dispatch(ServerRequest $request) Provides a way to process requests and routes. If the route does not exist it throws a RouteNotFoundException and if the route's controller does not return an HttpResponse it throws an UnexpectedValueException
 * @static void halt(HttpResponse $response) Stop the router
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
     * @param string $request_uri The request URI
     * @param string $request_method The request http method
     * @return array
     */
    public function match(string $request_uri, string $request_method): array {
        if(null !== $this->cors) {
            call_user_func($this->cors);
        }

        $request_uri = rawurldecode(parse_url($request_uri, PHP_URL_PATH));

        if('/' !== $request_uri) {
            $request_uri = rtrim($request_uri, '/\\');
        }

        $routes = $this->routes[$request_method] ?? []; 
        foreach ($routes as $route) {
            $pattern = $this->getRegexPattern($route->getPath());

            if (preg_match($pattern, $request_uri, $params)) {
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
     * @throws RuntimeException When the request uri don't match any route
     */
    public function dispatch(ServerRequest $request): HttpResponse {
        $router_params = $this->match($request->server->get('REQUEST_URI'), $request->server->get('REQUEST_METHOD'));

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
     * Stop the router
     * 
     * @param HttpResponse $response Response object
     */
    public static function halt(HttpResponse $response): void {
        SapiEmitter::emit($response);
        exit();
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