<?php declare(strict_types = 1);
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2022-2024 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @link      https://github.com/rguezque
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace rguezque\RouteCollection;

class HttpJsonResponse extends HttpResponse {
    public function __construct(array $body = [], int $status_code = 200, array $headers = []) {
        $body = json_encode($body, JSON_PRETTY_PRINT);
        parent::__construct($body, $status_code, $headers);
        $this->headers->setHeader('Content-Type', 'application/json;charset=utf-8');
    }

    public function setJson(array $data, bool $encode = true) {
        $result = $encode ? json_encode($data, JSON_PRETTY_PRINT) : $data;
        $this->body = $result;
        $this->headers->setHeader('Content-Type', 'application/json$json_header;charset=utf-8');
    }

    /**
     * Disable this method
     */
    public function setBody(string $body): void {}
}

?>