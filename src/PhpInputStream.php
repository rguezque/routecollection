<?php declare(strict_types = 1);
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2022-2024 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @link      https://github.com/rguezque
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace rguezque\RouteCollection;

/**
 * Represent a read-only stream
 * 
 * @method Collection getParsedStr() Parses the raw query string into variables
 * @method Collection getDecodedJson() Decode variables from a json string
 * @method string getRawData() Return the raw stream
 */
class PhpInputStream {

    /**
     * Is a read-only stream that allows reading data from the requested body
     * 
     * @var string
     */
    private $stream;

    public function __construct($body = 'php://input') {
        $this->stream = file_get_contents($body);
    }

    /**
     * Return the parsed raw query string into variables
     * 
     * @return Collection
     */
    public function getParsedStr(): Collection {
        parse_str($this->stream, $data);

        return new Collection($data);
    }

    /**
     * Return decoded variables from a json string
     * 
     * @return Collection
     */
    public function getDecodedJson(): Collection {
        $data = json_decode($this->stream, true);

        return new Collection($data);
    }

    /**
     * Return the raw stream
     * 
     * @return string
     */
    public function getRawData(): string {
        return $this->stream;
    }

}

?>