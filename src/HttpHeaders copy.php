<?php declare(strict_types = 1);
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2022-2024 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @link      https://github.com/rguezque
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace rguezque\RouteCollection;

/**
 * Represent an HTTP headers collection
 * 
 * @method void setHeader(string $name, $value) Add or update an HTTP header
 * @method mixed getHeader(string $name) Retrieve an HTTP header by name
 * @method bool hasHeader(string $name) Check if an HTTP header exists
 * @method void removeHeader(string $name) Remove an HTTP header
 * @method void clearAllHeaders() Remove all HTTP headers
 * @method array getAllHeaders() Get all HTTP headers
 * @method void sendHeaders() Send all HTTP headers
 */
class HttpHeaders {
    /**
     * Headers collection
     * 
     * @var array
     */
    private $headers = [];

    /**
     * Initialize the HTTP headers collection
     */
    public function __construct(array $headers = []) {
        $this->headers = $headers;
    }

    /**
     * Add or update an HTTP header
     * 
     * @param string $name Header name
     * @param mixed $value Header value
     * @return void
     */
    public function setHeader(string $name, $value): void {
        $this->headers[$name] = $value;
    }

    /**
     * Retrieve an HTTP header by name
     * 
     * @param string $name Header name
     * @return mixed
     */
    public function getHeader(string $name): mixed {
        return $this->headers[$name] ?? null;
    }

    /**
     * Check if an HTTP header exists
     * 
     * @param string $name Header name
     * @return bool
     */
    public function hasHeader(string $name): bool {
        return isset($this->headers[$name]);
    }

    /**
     * Remove an HTTP header
     * 
     * @param string $name Header name
     * @return void
     */
    public function removeHeader(string $name): void {
        if ($this->hasHeader($name)) {
            unset($this->headers[$name]);
        }
    }

    /**
     * Remove all HTTP headers
     * 
     * @return void
     */
    public function clearAllHeaders(): void {
        $this->headers = [];
    }

    /**
     * Get all HTTP headers
     * 
     * @return array
     */
    public function getAllHeaders(): array {
        return $this->headers;
    }

    /**
     * Send all HTTP headers
     * 
     * @return void
     */
    public function sendHeaders(): void {
        foreach ($this->headers as $name => $value) {
            header("{$name}: {$value}");
        }
    }
}


?>