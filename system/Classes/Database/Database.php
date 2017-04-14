<?php

namespace Asylamba\Classes\Database;

class Database {
	/** @var string **/
	protected $name;
	/** @var \PDO **/
	private $connection;
	/** @var int **/
	private static $nbrOfQuery = 0;

	/**
	 * @return int
	 */
	public static function getNbrOfQuery() {
		return self::$nbrOfQuery;
	}

	/**
	 * @param string $host
	 * @param string $name
	 * @param string $user
	 * @param string $password
	 */
	public function __construct($host, $name, $user, $password) {
		try {
			$this->name = $name;
			$this->connection = new \PDO("mysql:dbname=$name;host=$host;charset=utf8", $user, $password, [
				\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
				\PDO::ATTR_EMULATE_PREPARES => false
			]);
		} catch (\PDOException $e) {
			die('Erreur de connection à la base de données : ' . $e->getMessage());
		}
	}
	
	public function init($dumpFile)
	{
		$statement = $this->query("SHOW TABLES FROM {$this->name}");
		if ($statement->fetch() !== false) {
			return;
		}
		$this->execute(file_get_contents($dumpFile));
	}

	public function query($query) {
		self::$nbrOfQuery++;
		return $this->connection->query($query);
	}
	
	public function prepare($query) {
		self::$nbrOfQuery++;
		return $this->connection->prepare($query);
	}
	
	public function execute($query) {
		return $this->connection->exec($query);
	}
	
	public function lastInsertId() {
		return $this->connection->lastInsertId();
	}
	
	public function quote($query) {
		return $this->connection->quote($query);
	}
}
