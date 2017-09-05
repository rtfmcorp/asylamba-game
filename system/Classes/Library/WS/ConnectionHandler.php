<?php

namespace Asylamba\Classes\Library\WS;

use Asylamba\Classes\Daemon\Client;

use Asylamba\Classes\Library\Http\Response;

class ConnectionHandler
{
    /** @var string **/
    protected $serverHost;
    
    const CLOSE_FRAME = "\x03\xe9";
    
    const EVENT_NEWS_CREATION = 'news_creation';
    
    /**
     * @param string $serverHost
     */
    public function __construct($serverHost)
    {
        $this->serverHost = $serverHost;
    }
    
    const WEBSOCKET_KEY_SUFIX = '258EAFA5-E914-47DA-95CA-C5AB0DC85B11';
    
    public function handle(Client $client)
    {
        if (($payload = $client->getWsConnection()->receive()) === false || $payload === 'PING') {
            return $this->pong($client);
        }
        if ($payload === self::CLOSE_FRAME) {
            return $this->close($client->getWsConnection()->getSocket());
        }
        \Asylamba\Classes\Daemon\Server::debug($payload);
        return true;
    }
    
    public function handshake($input, $wsKey)
    {
        $response = new Response();
        $response->setProtocol('HTTP/1.1');
        $response->setStatusCode(101);
        $response->headers->set('Connection', 'Upgrade');
        $response->headers->set('Upgrade', 'websocket');
        $response->headers->set('Sec-WebSocket-Accept', base64_encode(pack('H*', sha1($wsKey . self::WEBSOCKET_KEY_SUFIX))));
        $response->headers->set('Sec-WebSocket-Origin', "http://{$this->serverHost}/");
        $response->headers->set('Sec-WebSocket-Location', "ws://{$this->serverHost}/");
        
        fputs($input, $response->send());
    }
    
    public function pong(Client $client)
    {
        fputs($client->getWsConnection()->getSocket(), 'PONG', 4);
        return true;
    }
    
    public function close($stream)
    {
        fputs($stream, self::CLOSE_FRAME);
        fclose($stream);
        return false;
    }
}