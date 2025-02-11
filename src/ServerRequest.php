<?php declare(strict_types = 1);
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2022-2024 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @link      https://github.com/rguezque
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace rguezque\RouteCollection;

/**
 * Represent the current request
 * 
 * @method ServerRequest withGlobals(array $get, array $post, array $server, array $cookie, array $files, array $params) Return an instance of ServerRequest with custom data for globals
 * @method void withParams(array $params) Allows set new values to route params
 * @method Collection getRequestHeaders() Retrieve all HTTP headers from the current request
 * @method string buildQuery(string $uri, array $params) Generate URL-encoded query string
 */
class ServerRequest {
    /**
     * $_GET params
     * 
     * @var Collection
     */
    public readonly Collection $query;

    /**
     * $_POST params
     * 
     * @var Collection
     */
    public readonly Collection $body;

    /**
     * $_SERVER params
     * 
     * @var Collection
     */
    public readonly Collection $server;

    /**
     * $_COOKIE params
     * 
     * @var Collection
     */
    public readonly Collection $cookie;

    /**
     * $_FILES params
     * 
     * @var Collection
     */
    public readonly Collection $files;

    /**
     * Router params
     * 
     * @var Collection
     */
    public readonly Collection $params;

    /**
     * HTTP headers
     * 
     * @var Collection
     */
    public readonly Collection $headers;
    
    /**
     * PHP Input Stream (php://input)
     * 
     * @var PhpInputStream
     */
    public readonly PhpInputStream $input;

    public function __construct() {
        $this->query = new Collection($_GET);
        $this->body = new Collection($_POST);
        $this->server = new Collection($_SERVER);
        $this->cookie = new Collection($_COOKIE);
        $this->files = new Collection($_FILES);
        $this->params = new Collection;
        $this->input = new PhpInputStream;
        $this->headers = new Collection(getallheaders());
    }

    /**
     * Allows set new values to route params
     * 
     * @param array $params Array data
     * @return void
     */
    public function withParams(array $params): void {
        $this->params = new Collection($params);
    }

    /**
     * Generate URL-encoded query string
     * 
     * @param string $uri URI to construct query
     * @param array $params Params to construct query
     * @return string
     */
    public static function buildQuery(string $uri, array $params): string {
        return trim($uri).'?'.http_build_query($params);
    }

}

?>