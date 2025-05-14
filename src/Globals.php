<?php declare(strict_types = 1);
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2022-2024 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @link      https://github.com/rguezque
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace rguezque\RouteCollection;

use rguezque\RouteCollection\Interfaces\GlobalsCollectionInterface;

/**
 * Manage the $GLOBALS parameters
 * 
 * @method mixed get(string $name, mixed $default = null) Return a global param by name
 * @method void set(string $name, mixed $value) Set or overwrite a global param
 * @method array all() Return all global params array
 * @method bool has(string $name) Return true if a global param exists and is different to null, otherwise false
 * @method void remove(string $name) Remove a global parameter
 * @method void clear() Remove all global params
 */
class Globals implements GlobalsCollectionInterface {
    /**
     * Disable constructor
     */
    private function __construct() {}

    /**
     * Return a global parameter by name
     * 
     * @param string $name Global param name
     * @param mixed $default Default value to return
     * @return mixed
     */
    public static function get(string $name, mixed $default = null): mixed {
        return self::has($name) ? $GLOBALS[$name] : $default;
    }

    /**
     * Set or overwrite a global parameter
     * 
     * @param string $name Parameter name
     * @param mixed $value Parameter value
     * @return void
     */
    public static function set(string $name, mixed $value): void {
        $GLOBALS[$name] = $value;
    }

    /**
     * Return all globals parameters array
     * 
     * @return array
     */
    public static function all(): array {
        return $GLOBALS;
    }

    /**
     * Return true if a global parameter exists and is different to null, otherwise false
     * 
     * @param string $name Global parameter name
     * @return bool
     */
    public static function has(string $name): bool {
        return isset($GLOBALS[$name]);
    }

    /**
     * Remove a global parameter
     * 
     * @param string $name Parameter name
     * @return void
     */
    public static function remove(string $name): void {
        if(self::has($name)) {
            unset($GLOBALS[$name]);
        }
    }

    /**
     * Remove all globals parameters
     * 
     * @return void
     */
    public static function clear(): void {
        foreach($GLOBALS as $global) {
            unset($GLOBALS[$global]);
        }
    }
}

?>