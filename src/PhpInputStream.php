<?php declare(strict_types = 1);
/**
 * @author    Luis Arturo Rodríguez
 * @copyright Copyright (c) 2022-2024 Luis Arturo Rodríguez <rguezque@gmail.com>
 * @link      https://github.com/rguezque
 * @license   https://opensource.org/licenses/MIT    MIT License
 */

namespace rguezque\RouteCollection;

use InvalidArgumentException;

/**
 * Represent a read-only stream
 * 
 * @method Collection|string getStream()
 */
class PhpInputStream {
    /**
     * Flag to return php-input-stream raw data
     * 
     * @var int
     */
    const RAW_DATA = 1;

    /**
     * Flag to parse string php-input-stream
     * 
     * @var int
     */
    const PARSED_STRING = 2;

    /**
     * Flag to decode php-input-string from JSON format
     * 
     * @var int
     */
    const DECODED_JSON = 3;

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
     * Return the php input stream. Use flags PhpInputStream::RAW_DATA (default), PhpInputStream::PARSED_STRING or PhpInputStream::DECODED_JSON
     * 
     * With PhpInputStream::RAW_DATA returns the raw data, PhpInputStream::PARSED_STRING to parse the query string, PhpInputStream::DECODED_JSON to decode the stream when is a JSON string.
     * The data is wrapped an retrieved in a Collection object, except for PhpInputStream::RAW_DATA
     * 
     * @param int $format Flag for data format type to retrieve.
     * @return Collection|string
     * @throws  InvalidArgumentException When the flag is not valid
     */
    public function getStream(int $format = PhpInputStream::RAW_DATA): Collection|string {
        switch ($format) {
            case self::RAW_DATA:
                $data = $this->getRawData();
                break;
            case self::PARSED_STRING:
                $data = $this->getParsedStr();
                break;
            case self::DECODED_JSON:
                $data = $this->getDecodedJson();
                break;
            default:
                throw new InvalidArgumentException('Invalid flag code for PHP Input Stream');
        }

        return $data;
    }

    /**
     * Return the parsed raw query string into variables
     * 
     * @return Collection
     */
    private function getParsedStr(): Collection {
        parse_str($this->stream, $data);

        return new Collection($data);
    }

    /**
     * Return decoded variables from a json string
     * 
     * @return Collection
     */
    private function getDecodedJson(): Collection {
        $data = json_decode($this->stream, true);

        return new Collection($data);
    }

    /**
     * Return the raw stream
     * 
     * @return string
     */
    private function getRawData(): string {
        return $this->stream;
    }

}

?>