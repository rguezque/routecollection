<?php declare(strict_types = 1);

namespace rguezque\RouteCollection;

class SapiEmitter extends HttpResponse {

    /**
     * Send the response
     * 
     * @return void
     */
    public static function emit(HttpResponse $response): void {
        // Set the status code
        //http_response_code($response->status_code);
        (new HttpStatus($response->status_code))->sendHttpStatus();

        // Send the headers
        if(!headers_sent()) {
            $response->headers->sendHeaders();
        }

        // Output the body
        $response->body->rewind();
        echo $response->body->getContents();
    }
}

?>