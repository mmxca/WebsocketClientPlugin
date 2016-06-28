<?php
namespace WebSocketClientPlugin\Controller\Component;

use Cake\Log\Log;
use WebSocketClientPlugin\Controller\Component\WebsocketClient;
use WebSocketClientPlugin\Controller\Component\Exceptions\ChainConnectException;
use WebSocketClientPlugin\Controller\Component\Exceptions\ChainSendException;
use WebSocketClientPlugin\Controller\Component\Exceptions\ChainReadException;
use WebSocket\ConnectionException;

/** 
 * BitmessageComponent is a class adapting the PhpBitmessage class that was developed 
 * by Convertor as copyrighted below.
 * 
 * Original class can be found here: 
 * @website http://conver.github.io/class.bitmessage.php/
 */
class WebsocketClientComponent extends Component {

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [];
    

    /**
     *
     * @var string 
     */
    private $url;
    
    /**
     *
     * @var WebsocketClient
     */
    private $websocketClient;
    
    public function initialize(array $config = []) {
        parent::initialize($config);
        Log::debug('initialize()');
    }    
    
    /**
     * Attempts to connect to the URL specified by $url and returns true if
     * successful.
     * 
     * @param type $url
     * @return boolean 
     * @throws ChainConnectException
     */
    public function connect($url) {
        Log::debug("connect(".$url.")");
        
        try {
            $this->url = $url;
            $this->websocketClient = new ChainClient($url);
            return true;
        } catch (ConnectionException $ex) {
            throw new ChainConnectException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }
    
    /**
     * Send the $outboundMessage to the Websocket as a string
     * 
     * @param string|array $outbound_message
     * @throws ChainSendException
     */
    public function send($outbound_message) {
        Log::debug("send(".$outbound_message.")");
        
        $message = (is_array($outbound_message)) ? json_encode($outbound_message) : $outbound_message;
        Log::debug("send(".$outbound_message."):message=".$message);

        try {
            $this->websocketClient->send($outbound_message);
            Log::debug("send(".$outbound_message."):message=".$message.":sent");
        } catch (ConnectionException $ex) {
            Log::error("send(".$outbound_message."):ConnectionException=".$ex->getMessage());
            throw new ChainSendException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * Reads the socket and returns the message as a string.  Returns null if there
     * was nothing to read.
     * 
     * @return null|string
     * @throws ChainReceiveException
     */
    public function read() {
        Log::debug("read()");
        try {
            $message = $this->websocketClient->receive();
            Log::debug("read():message=".$message);

            if (!(is_null($message))) {
                Log::debug("read():message=".$message.":Read");
                return $message;
            } else {
                Log::debug("read():message=".$message.":Nothing Read");
            }
            return null;
        } catch (ConnectionException $ex) {
            Log::error("read():ConnectionException=".$ex->getMessage());
            throw new ChainReadException($ex->getMessage(), $ex->getCode(), $ex);
        }
    }

    /**
     * Returns true if the connection is currently active
     * 
     * @return boolean
     */
    public function isConnected() {
        Log::debug("isConnected()");
        return $this->websocketClient->isConnected();
    }

}
