<?php declare(strict_types = 1);
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2022-2024 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @link      https://github.com/rguezque
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace rguezque\RouteCollection;

/**
 * Represent a collection or parameters
 * 
 * @method mixed get(string $name, mixed $default = null) Return a param by name
 * @method void set(string $name, mixed $value) Set or overwrite a param
 * @method array all() Return all params array
 * @method bool has(string $name) Return true if a param exists and is different to null, otherwise false
 * @method void remove(string $name) Remove a param by name
 * @method void clear() Remove all params
 */
class Collection {

    /**
     * Params collection
     * 
     * @var array
     */
    protected $data = [];

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
     * @param mixed $default Default value to return
     * @return mixed
     */
    public function get(string $name, mixed $default = null): mixed {
        return $this->has($name) ? $this->data[$name] : $default;
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
     * Remove a param by name
     * 
     * @param string $name Param name
     * @return void
     */
    public function remove(string $name): void {
        if($this->has($name)) {
            unset($this->data[$name]);
        }
    }

    /**
     * Remove all params
     * 
     * @return void
     */
    public function clear(): void {
        $this->data = [];
    }
}

?>