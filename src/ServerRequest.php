<?php declare(strict_types = 1);
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2022-2024 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @link      https://github.com/rguezque
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace rguezque\RouteCollection;

class ServerRequest {
    /**
     * $_GET params
     * 
     * @var Collection
     */
    public $get;

    /**
     * $_POST params
     * 
     * @var Collection
     */
    public $post;

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
     * @var string
     */
    public $input;

    public function __construct() {
        $this->get = new Collection($_GET);
        $this->post = new Collection($_POST);
        $this->server = new Collection($_SERVER);
        $this->cookie = new Collection($_COOKIE);
        $this->files = new Collection($_FILES);
        $this->params = new Collection;
        $this->input = file_get_contents('php://input');
    }

    /**
     * Return an instance of ServerRequest with custom data for globals
     * 
     * @param array $get $_GET data
     * @param array $get $_POST data
     * @param array $get $_SERVER data
     * @param array $get $_COOKIE data
     * @param array $get $_FILES data
     * @param array $get Router params
     * @return ServerRequest
     */
    public static function withGlobals(array $get, array $post, array $server, array $cookie, array $files, array $params): ServerRequest {
        $request = new ServerRequest;
        $request->get = new Collection($get);
        $request->post = new Collection($post);
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