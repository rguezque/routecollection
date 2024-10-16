<?php declare(strict_types = 1);
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2022-2024 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @link      https://github.com/rguezque
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace rguezque\RouteCollection;

use Exception;

/**
 * Represent an HTTP response
 * 
 * @method void clear() Reset the initial values for response
 * @method void setStatusCode(int $code) Set the HTTP status code
 * @method int getStatusCode() Get the HTTP status code
 * @method void setBody(string $body) Set the body content. Multiple calls appends to the content
 * @method string getBody() Get the body content
 * @method void send() Send the response
 */
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

    /**
     * Get the body content
     * 
     * @return string
     */
    public function getBody(): string {
        return $this->body;
    }

    /**
     * Send the response
     * 
     * @return void
     */
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