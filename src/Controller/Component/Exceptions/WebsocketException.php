<?php

namespace WebSocketClientPlugin\Controller\Component\Exceptions;

class WebsocketException extends \Exception {

    public function __construct($message, $code, $previous) {
        parent::__construct($message, $code, $previous);
    }

}
