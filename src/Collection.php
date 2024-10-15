<?php declare(strict_types = 1);
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2022-2024 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @link      https://github.com/rguezque
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace rguezque\RouteCollection;

use JsonSerializable;

class Collection implements CollectionInterface, JsonSerializable {

    /**
     * Params collection
     * 
     * @var array
     */
    private $data = [];

    /**
     * Initialize the params collection
     * 
     * @param array $data Params array
     */
    public function __construct(array $data = []) {
        $this->data = $data;
    }

    /**
     * Return a param by name
     * 
     * @param string $name Param name
     * @return mixed
     */
    public function get(string $name): mixed {
        return $this->data[$name];
    }

    /**
     * Set or overwrite a param
     * 
     * @param string $name Param name
     * @param mixed $value Param value
     * @return void
     */
    public function set(string $name, mixed $value): void {
        $this->data[$name] = $value;
    }

    /**
     * Return all params array
     * 
     * @return array
     */
    public function all(): array {
        return $this->data;
    }

    /**
     * Return true if a param exists and is different to null, otherwise false
     * 
     * @param string $name Param name
     * @return bool
     */
    public function has(string $name): bool {
        return isset($this->data[$name]);
    }

    /**
     * Remove all params
     * 
     * @return void
     */
    public function clear(): void {
        $this->data = [];
    }

    /**
     * Specify data which should be serialized to JSON when the object is used in json_encode
     * 
     * @return array
     */
    public function jsonSerialize(): array {
        return $this->data;
    }
}

?>