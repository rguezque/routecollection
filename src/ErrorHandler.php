<?php declare(strict_types = 1);
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2022-2024 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @link      https://github.com/rguezque
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace rguezque\RouteCollection;

use ArgumentCountError;
use ErrorException;
use Exception;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

/**
 * Configure the app environment and error handler
 * 
 * @static void configure(array $options) Handler options (environment, timezone, log_path)
 */
class ErrorHandler {

    /**
     * Environment mode
     * 
     * @var string
     */
    private $environment;

    /**
     * Errors log path
     * 
     * @var string
     */
    private $log_path;

    /**
     * Setup the environment, timezone and error handler
     * 
     * @param array $options Handler options (environment, timezone, log_path)
     * @throws ArgumentCountError
     * @throws RuntimeException
     */
    private function __construct(array $options) {
        if(0 < count($params = array_diff(['log_path','timezone'], array_keys($options)))) {
            throw new ArgumentCountError(sprintf('Missing options definition for %s.', implode(', ', $params)));
        }
        
        $this->log_path = $options['log_path'];
        $this->environment = $options['environment'] ?? 'development';
        if(!in_array($this->environment, ['production', 'development'])) {
            $this->environment = 'development';
        }

        $timezone =  $options['timezone'] ?? 'UTC';
        if(!in_array($timezone, timezone_identifiers_list())) {
            $timezone = 'UTC';
        }

        date_default_timezone_set($timezone);
        $this->setMode();
        $this->setHandler();

        // Try to create the directory for logs if not exists
        if(!file_exists($this->log_path)) {
            if(!mkdir($this->log_path, 0777, true)) {
                throw new RuntimeException('Error creating directory for logs. Please create it manually.');
            }
        }
    }

    /**
     * Set the environment
     *
     * @return void
     * @throws InvalidArgumentException
     */
    private function setMode(): void {
        switch ($this->environment) {
            case 'development':
                error_reporting(E_ALL);
                ini_set('display_errors', '1');
                ini_set('display_startup_errors', '1');
                break;

            case 'production':
                error_reporting(0);
                ini_set('display_errors', '0');
                ini_set('display_startup_errors', '0');
                break;

            default:
                throw new InvalidArgumentException('Only is allowed "production" or "development" values for application environment definition.');
        }
    }

    /**
     * Init the error handler
     * 
     * @return void
     */
    private function setHandler(): void {
        set_error_handler(array($this, 'errorHandler'));
        set_exception_handler(array($this, 'exceptionHandler'));
    }

    /**
     * Error handler
     *
     * @param int $errno Error number
     * @param string $errstr Error string
     * @param string $errfile Error file
     * @param int $errline Error line
     * @return void
     * @throws ErrorException
     */
    public function errorHandler(int $errno, string $errstr, string $errfile, int $errline): void {
        if ($errno & error_reporting()) {
            throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
        }
    }

    /**
     * Exception handler
     *
     * @param Exception $e Excepction threw
     * @return void
     */
    public function exceptionHandler($e): void {
        // Vuelca el registro de errores a un archivo log
        $log_path = rtrim($this->log_path, '/\\').'/';

        ini_set('log_errors', '1');
        ini_set('error_log', sprintf('%serrors.log', $log_path));

        $err_string = sprintf('[%s] %s%s', $e->getCode(), $e->getMessage().PHP_EOL, $e->getTraceAsString().PHP_EOL);
        error_log($err_string);

        'development' == $this->environment ? $this->showException($e) : $this->showProductionException();
    }

    /**
     * Throws error in development mode
     *
     * @param Exception $e Exception threw
     * @return void
     */
    private function showException($e): void {
        $string = sprintf('<h1>500</h1><h3>Internal Server Error</h3>%s [%s]<br><pre>%s</pre>', $e->getMessage(), $e->getCode(), $e->getTraceAsString());
        
        try {
            $response = new HttpResponse($string, 500);
            SapiEmitter::emit($response);
        } catch (Throwable $t) { // PHP 7.0+
            exit($string);
        } catch(Exception $e) { // PHP < 7
            exit($string);
        }
    }

    /**
     * Throws error in production mode
     * 
     * @return void
     */
    private function showProductionException(): void {
        $string = sprintf('<h1>%s</h1><h3>%s</h3>', 'Oops!', 'Something gone wrong.');
        $response = new HttpResponse($string, 500);
        SapiEmitter::emit($response);
    }

    /**
     * Create a singleton of Handler
     * 
     * @param array $options Options array
     * @return void
     */
    public static function configure(array $options): void {
        static $instance;

        if(!$instance) {
            $instance = new ErrorHandler($options);
        }
    }

}
