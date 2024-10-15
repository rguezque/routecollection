<?php declare(strict_types = 1);
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2022-2024 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @link      https://github.com/rguezque
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace rguezque\RouteCollection;

use PDO;
use PDOException;

/**
 * Create a singleton PDO connection to a database. Needs connection parameters from a .env file previously loaded ('DB_DSN', 'DB_USERNAME', 'DB_PASSWORD')
 * 
 * @method PDO getInstance
 */
class PdoSingleton {
    /**
     * PDO instance
     * 
     * @var PDO
     */
    private static $pdo;

    /**
     * Creates the PDO instance representing a connection to a database
     * 
     * @throws PDOException
     */
    private function __construct() {
        $dsn = $_ENV['DB_DSN'];
        $username = $_ENV['DB_USERNAME'];
        $password = $_ENV['DB_PASSWORD'];

        try {
            $this->pdo = new PDO($dsn, $username, $password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new PDOException('Connection failed: ' . $e->getMessage()) . PHP_EOL . $e->getTraceAsString();
        }
    }

    /**
     * Return the PDO instance
     * 
     * @return PDO
     */
    public static function getInstance(): PDO {
        if (self::$pdo === null) {
            self::$pdo = new self();
        }

        return self::$pdo;
    }

    /**
     * Prevents colnation and unserialized
     */
    private function __clone() {}
    private function __wakeup() {}
}
