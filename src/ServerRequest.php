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
    public $query;

    /**
     * $_POST params
     * 
     * @var Collection
     */
    public $body;

    /**
     * $_SERVER params
     * 
     * @var Collection
     */
    public $server;

    /**
     * $_COOKIE params
     * 
     * @var Collection
     */
    public $cookie;

    /**
     * $_FILES params
     * 
     * @var Collection
     */
    public $files;

    /**
     * Router params
     * 
     * @var Collection
     */
    public $params;
    
    /**
     * PHP Input Stream (php://input)
     * 
     * @var PhpInputStream
     */
    public $input;

    public function __construct() {
        $this->query = new Collection($_GET);
        $this->body = new Collection($_POST);
        $this->server = new Collection($_SERVER);
        $this->cookie = new Collection($_COOKIE);
        $this->files = new Collection($_FILES);
        $this->params = new Collection;
        $this->input = new PhpInputStream;
    }

    /**
     * Return an instance of ServerRequest with custom data for globals
     * 
     * @param array $query $_GET data
     * @param array $body $_POST data
     * @param array $server $_SERVER data
     * @param array $cookie $_COOKIE data
     * @param array $get $_FILES data
     * @param array $get Router params
     * @return ServerRequest
     */
    public static function withGlobals(array $query, array $body, array $server, array $cookie, array $files, array $params): ServerRequest {
        $request = new ServerRequest;
        $request->query = new Collection($query);
        $request->body = new Collection($body);
        $request->server = new Collection($server);
        $request->cookie = new Collection($cookie);
        $request->files = new Collection($files);
        $request->params = new Collection($params);

        return $request;
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
     * Retrieve all HTTP headers from the current request
     * 
     * @return Collection
     */
    public function getRequestHeaders(): Collection {
        $headers =  getallheaders();
        return new Collection($headers);
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