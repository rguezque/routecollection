<?php declare(strict_types = 1);
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2022-2024 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @link      https://github.com/rguezque
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace rguezque\RouteCollection;

/**
 * Manage the $_SESSION variables and session data
 * 
 * @method SessionManager getInstance() Return a singleton instance of SessionManager
 * @method SessionManager start() Start new or resume existing session
 * @method bool isStarted() Return true if a session is initialized
 * @method void set(string $key, mixed $value) Set a session var
 * @method mixed get(string $key) Retrieve a session var
 * @method void remove(string $key) Remove a session variable by name
 * @method void destroy() Free session variables and destroys all data registered to a session
 * @method bool exists(string $key) Return true if a session variable exists, otherwise false
 * @method array getAll() Retrieve all the session variables
 */
class SessionManager {
    /**
     * Store the singleton instance of SessionManager
     * 
     * @var SessionManager
     */
    private static $instance;

    /**
     * Constructor disabled for singleton
     */
    private function __construct() {}

    /**
     * Return a singleton instance of SessionManager
     * 
     * @return SessionManager
     */
    public static function getInstance(): SessionManager {
        if (!self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Start new or resume existing session
     * 
     * @return SessionManager
     */
    public function start(): SessionManager {
        if(!$this->isStarted()) {
            session_start();
        }

        return $this;
    }

    /**
     * Return true if a session is initialized
     * 
     * @return bool
     */
    public function isStarted(): bool {
        return session_status() === PHP_SESSION_ACTIVE;
    }

    /**
     * Set a session var
     * 
     * @param string $name Session variable name
     * @param mixed $value Session variable value
     * @return void
     */
    public function set(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }

    /**
     * Retrieve a session var
     * 
     * @param string $key Session variable name
     * @return mixed
     */
    public function get(string $key): mixed {
        return $_SESSION[$key] ?? null;
    }

    /**
     * Remove a session variable by name
     * 
     * @param string $key Session variable name
     * @return void
     */
    public function remove(string $key): void {
        unset($_SESSION[$key]);
    }

    /**
     * Free session variables and destroys all data registered to a session
     * 
     * @return void
     */
    public function destroy(): void {
        session_unset();
        session_destroy();
    }

    /**
     * Return true if a session variable exists, otherwise false
     * 
     * @param string $key Session variable name
     * @return bool
     */
    public function exists(string $key): bool {
        return isset($_SESSION[$key]);
    }

    /**
     * Retrieve all the session variables
     * 
     * @return array
     */
    public function getAll(): array {
        return $_SESSION;
    }
}

?>