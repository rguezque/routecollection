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
        $this->headers->setHeader('Content-Type', 'application/json;charset=utf-8');
    }

    /**
     * Set the response body
     * 
     * @param array|string $data Data for response as json
     * @param bool $encode If true, encode the data to json
     * @return void
     */
    public function setJson(array|string $data, bool $encode = true): void {
        $result = $encode ? json_encode($data, JSON_PRETTY_PRINT) : $data;
        $this->body = $result;
    }

    /**
     * Disabled this method for HttpJsonResponse class
     * 
     * @return void
     */
    public function setBody(string $body): void {}
}

?>