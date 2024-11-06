<?php declare(strict_types=1);
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2022-2024 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @link      https://github.com/rguezque
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace rguezque\RouteCollection;

/**
 * Configure and enable CORS (Cross-Origin Resources Sharing)
 * 
 * @method CorsConfig addOrigin(string $origin, array $methods = [], array $headers = []) Add an allowed origin
 * @method void resolve(ServerRequest $request) Apply and execute the CORS configuration
 */
class CorsConfig {
    /**
     * Allowed origins
     * 
     * @param string[]
     */
    private $origins = [];

    /**
     * Default allowed request methods
     * 
     * @param string[]
     */
    private $default_methods = ['GET', 'POST'];

    /**
     * Default allowed http headers
     * 
     * @param string[]
     */
    private $default_headers = ['Content-Type', 'Accept', 'Authorization'];

    /**
     * Initialize the cors configuration for allowed origins
     * 
     * @param array $cors_origins Origins configuration array
     */
    public function __construct(array $cors_origins = []) {
        if([] !== $cors_origins) {
            foreach($cors_origins as $origin => $config) {
                $this->addOrigin($origin, $config['methods'] ?? [], $config['headers'] ?? []);
            }
        }
    }

    /**
     * Add an allowed origin
     * 
     * @param string $origin Allowed domain url
     * @param array $methods Allowed request methods from allowed domain
     * @param array $headers Allowed http headers from allowed domain
     * @return CorConfig
     */
    public function addOrigin(string $origin, array $methods = [], array $headers = []): CorsConfig {
        $this->origins[$origin] = [
            'methods' => [] !== $methods ? $methods : $this->default_methods,
            'headers' => [] !== $headers ? $headers : $this->default_headers
        ];

        return $this;
    }

    /**
     * Apply and execute the CORS configuration
     * 
     * @return void
     */
    public function __invoke(): void {
        $http_origin = $_SERVER['HTTP_ORIGIN'] ?? null;

        if(isset($http_origin)) {
            foreach ($this->origins as $origin => $config) {
                if (preg_match('#' . $origin . '#', $http_origin)) {
                    header("Access-Control-Allow-Origin: " . $http_origin);
                    header("Access-Control-Allow-Methods: " . implode(', ', $config['methods']));
                    header("Access-Control-Allow-Headers: " . implode(', ', $config['headers']));
                    header('Access-Control-Max-Age: 60'); //Maximum number of seconds the results can be cached
                }
            }
        }
    }
}

?>
