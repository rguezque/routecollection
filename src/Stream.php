<?php declare(strict_types = 1);

namespace rguezque\RouteCollection;

/**
 * Represent a PHP stream
 * 
 * @method int|false write(mixed $string) Write content to stream and return the total bytes written, or false on error
 * @method string|false read(int $length) Read the stream
 * @method string|false getContents() Retrieve the remainder content of the stream from current pointer position
 * @method mixed detach() Free and return the current stream
 * @method int getSize() Returns the bytes size of the stream
 * @method int tell() Return the current position of the stream read/write pointer
 * @method bool eof() Return true if the stream pointer is at end-of-file, otherwise false
 * @method void seek(int $offset, int $whence = SEEK_SET) Sets the file position indicator for the file referenced by stream. The new position, measured in bytes from the beginning of the file, is obtained by adding offset to the position specified by whence.
 * @method void rewind() Rewind the stream pointer at beginning
 * @method void close() Close the stream from writing
 */
class Stream {
    private $stream;

    /**
     * Initialize the stream
     * 
     * @param mixed $resource Resource of type stream
     */
    public function __construct(mixed $resource){
        $this->stream = $resource;
    }

    /**
     * Close the stream
     */
    public function __destruct() {
        $this->close();
    }

    /**
     * Write content to stream and return the total bytes written, or false on error
     * 
     * @param mixed $string
     * @return int|false
     */
    public function write(mixed $string): int|false {
        return fwrite($this->stream, $string);
    }

    /**
     * Read the stream 
     * 
     * @param int $length Up to length number of bytes read
     * @return string|false
     */
    public function read(int $length): string|false {
        return fread($this->stream, $length);
    }

    /**
     * Retrieve the remainder content of the stream from current pointer position
     * 
     * @return string|false
     */
    public function getContents(): string|false {
        return stream_get_contents($this->stream);
    }

    /**
     * Free and return the current stream
     * 
     * @return mixed
     */
    public function detach(): mixed {
        $stream = $this->stream;
        $this->stream = null;
        return $stream;
    }

    /**
     * Returns the bytes size of the stream
     * 
     * @return int
     */
    public function getSize(): int {
        return fstat($this->stream)['size'];
    }

    /**
     * Return the current position of the stream read/write pointer
     * 
     * @return int|false
     */
    public function tell(): int|false {
        return ftell($this->stream);
    }

    /**
     * Return true if the stream pointer is at end-of-file, otherwise false
     * 
     * @return bool
     */
    public function eof(): bool {
        return feof($this->stream);
    }

    /**
     * Sets the file position indicator for the file referenced by stream. The new position, measured in bytes from the beginning of the file, is obtained by adding offset to the position specified by whence.
     * 
     * @param int $offset The offset 
     * @param int $whence The whence
     * @return void
     */
    public function seek(int $offset, int $whence = SEEK_SET): void {
        fseek($this->stream, $offset, $whence);
    }

    /**
     * Rewind the stream pointer at beginning
     * 
     * @return void
     */
    public function rewind(): void {
        $this->seek(0);
    }

    /**
     * Close the stream from writing
     * 
     * @return void
     */
    public function close(): void {
        if ($this->stream) {
            fclose($this->stream);
            $this->stream = null;
        }
    }

    public function __toString() {
        return $this->getContents();
    }
}

?>