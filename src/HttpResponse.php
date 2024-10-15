<?php declare(strict_types = 1);
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2022-2024 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @link      https://github.com/rguezque
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace rguezque\RouteCollection;

class HttpResponse {
    /**
     * HTTP status code
     * 
     * @var int
     */
    private $status_code;

    /**
     * HTTP headers container
     * 
     * @var HttpHeaders
     */
    public $headers;

    /**
     * HTTP body
     * 
     * @var string
     */
    protected $body;

    /**
     * Initialize the http response
     * 
     * @param string $body The content of http response
     * @param int $status_code The http status code of response
     * @param array $headers HTTP headers for response
     */
    public function __construct(string $body = '', int $status_code = 200, array $headers = []) {
        $this->status_code = $status_code;
        $this->headers = [] !== $headers ? new HttpHeaders($headers) : new HttpHeaders;
        $this->body = $body;
    }

    /**
     * Reset the initial values for response
     * 
     * @return void
     */
    public function clear(): void {
        $this->status_code = 200;
        $this->headers->clearAllHeaders();
        $this->body = null;
    }

    /**
     * Set the HTTP status code
     * 
     * @param int $code HTTP status code
     * @return void
     */
    public function setStatusCode(int $code): void {
        $this->status_code = $code;
    }

    /**
     * Get the HTTP status code
     * 
     * @return int
     */
    public function getStatusCode(): int {
        return $this->status_code;
    }

    /**
     * Set the body content. Multiple calls appends to the content
     * 
     * @param string $body The content for response
     * @return void
     */
    public function setBody(string $body): void {
        $this->body .= $body;
    }

    // Get the body content
    public function getBody() {
        return $this->body;
    }

    // Send the response
    public function send() {
        // Set the status code
        http_response_code($this->status_code);

        // Send the headers
        if(!headers_sent()) {
            $this->headers->sendHeaders();
        }

        // Output the body
        echo $this->body;
    }
}

?>