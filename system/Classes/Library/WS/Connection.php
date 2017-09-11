<?php

namespace Asylamba\Classes\Library\WS;

use Wrench\Frame\HybiFrame;

class Connection
{
    /** @var resource **/
    protected $socket;

    const DEFAULT_BUFFER_LENGTH = 1400;
    
    /**
     * @param resource $socket
     */
    public function __construct($socket)
    {
        $this->socket = $socket;
    }
    
    public function send($payload)
    {
        if (get_resource_type($this->socket) === 'Unknown') {
            $this->socket = null;
            return false;
        }
        $frame = new HybiFrame();
        $frame->encode($payload);
        $buffer = $frame->getFrameBuffer();
        fputs($this->socket, $buffer);
        return true;
    }
    
    public function receive()
    {
        $buffer = fread($this->socket, self::DEFAULT_BUFFER_LENGTH);
        if (empty($buffer)) {
            return false;
        }
        
        $frame = new HybiFrame();
        $frame->receiveData($buffer);
        
        return $frame->getFramePayload();
    }
    
    public function getSocket()
    {
        return $this->socket;
    }
}
