<?php declare(strict_types = 1);
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2022-2024 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @link      https://github.com/rguezque
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace rguezque\RouteCollection;

/**
 * Represent an HTTP response as JSON
 */
class HttpJsonResponse extends HttpResponse {
    public function __construct(array $data = [], int $status_code = 200, array $headers = []) {
        if([] !== $data) {
            $data = json_encode($data, JSON_PRETTY_PRINT);
        }
        parent::__construct($data, $status_code, $headers);
        $this->headers->set('Content-Type', 'application/json;charset=utf-8');
    }
}

?>