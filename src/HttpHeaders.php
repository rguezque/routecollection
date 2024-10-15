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
     * Add or update a header
     * 
     * @param string $name Header name
     * @param mixed $value Header value
     * @return void
     */
    public function setHeader(string $name, $value): void {
        $this->headers[$name] = $value;
    }

    /**
     * Retrieve a header by name
     * 
     * @param string $name Header name
     * @return mixed
     */
    public function getHeader(string $name) {
        return $this->headers[$name] ?? null;
    }

    /**
     * Check if a header exists
     * 
     * @param string $name Header name
     * @return bool
     */
    public function hasHeader(string $name): bool {
        return isset($this->headers[$name]);
    }

    /**
     * Remove a header
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
     * Remove all headers
     * 
     * @return void
     */
    public function clearAllHeaders(): void {
        $this->headers = [];
    }

    /**
     * Get all headers
     * 
     * @return array
     */
    public function getAllHeaders(): array {
        return $this->headers;
    }

    /**
     * Send all headers
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