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
class HttpHeaders extends Collection {
    
    /**
     * Initialize the HTTP headers collection
     */
    public function __construct(array $headers = []) {
        parent::__construct($headers);
    }


    /**
     * Send all HTTP headers
     * 
     * @return void
     */
    public function sendHeaders(): void {
        foreach ($this->data as $name => $value) {
            header("{$name}: {$value}");
        }
    }
}


?>