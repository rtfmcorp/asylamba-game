<?php

namespace Asylamba\Classes\Redis;

class RedisManager
{
    /** @var \Redis **/
    protected $connection;
    
    /**
     * @param string $host
     * @param int $port
     * @param string $password
     * @param float $timeout
     */
    public function __construct($host, $port, $password, $timeout)
    {
        $this->connection = new \Redis();
        $this->connection->connect($host, $port, $timeout);
        $this->connection->auth($password);
    }
    
    public function __destruct()
    {
        $this->connection->close();
    }
    
    /**
     * @return \Redis
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
