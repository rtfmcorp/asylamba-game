<?php

namespace App\Classes\Redis;

class RedisManager
{
	protected \Redis $connection;

	public function __construct(string $host, int $port, ?string $password, float $timeout)
	{
		$this->connection = new \Redis();
		$this->connection->connect($host, $port, $timeout);
		if (null !== $password) {
			$this->connection->auth($password);
		}
	}
	
	public function __destruct()
	{
		$this->connection->close();
	}

	public function getConnection(): \Redis
	{
		return $this->connection;
	}
}
