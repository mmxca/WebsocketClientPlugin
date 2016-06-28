<?php
namespace WebSocketClientPlugin\Controller\Component;

use WebSocket\ConnectionException;
use Cake\Log\Log;

class WebsocketClient extends \WebSocket\Client {

    /**
     * Extends the parent receive method to deal with status, ping, and empty reads
     * when communicating with the websocket.
     * 
     * @return type
     * @throws ConnectionException
     */
    public function receive() {
        Log::debug("receive()");
        try {
            $message = parent::receive();
            Log::debug("receive():message=".$message);
            
            $j_message = json_decode($message, true);
            Log::debug("receive():j_message=".print_r($j_message, true));

            if (isset($j_message["status"])) {
                Log::debug("receive():status=".$j_message["status"]);
                return null;
            }
            if (isset($j_message["type"])) {
                Log::debug("receive():type=".$j_message["type"]);
                if ($j_message["type"] == "ping") {
                    Log::debug("receive():type=".$j_message["type"].":Ping Received");
                    return null;
                }
            }

            Log::debug("receive():message=".$message.":Valid Message");
            return $message;
        } catch (ConnectionException $ex) {
            if ($this->startsWith($ex->getMessage(), "Empty read")) {
                Log::debug("receive():empty");
                return null;
            }
            Log::error("receive():code=".$ex->getCode().":message=".$ex->getMessage());
            throw $ex;
        }
    }

    private function startsWith($haystack, $needle) {
        return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
    }

}
