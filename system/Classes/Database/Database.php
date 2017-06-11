<?php

namespace Asylamba\Classes\Database;

class Database {
	/** @var string **/
	protected $name;
	/** @var string **/
	protected $host;
	/** @var string **/
	protected $user;
	/** @var string **/
	protected $password;
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
		$this->host = $host;
		$this->name = $name;
		$this->user = $user;
		$this->password = $password;
		$this->refresh();
	}
	
	public function init($dumpFile)
	{
		$statement = $this->query("SHOW TABLES FROM {$this->name}");
		if ($statement->fetch() !== false) {
			return;
		}
		$this->execute(file_get_contents($dumpFile));
	}
	
	public function refresh()
	{
		try {
			// Close previous connection
			$this->connection = null;
			$this->connection = new \PDO(
				"mysql:dbname={$this->name};host={$this->host};charset=utf8",
				$this->user,
				$this->password,
				[
					\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
					\PDO::ATTR_EMULATE_PREPARES => false,
					\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC
				]
			);
		} catch (\PDOException $e) {
			die('Erreur de connection à la base de données : ' . $e->getMessage());
		}
	}
	
	/**
	 * @return bool
	 */
	public function beginTransaction()
	{
		return $this->connection->beginTransaction();
	}
	
	/**
	 * @return bool
	 */
	public function inTransaction()
	{
		return $this->connection->inTransaction();
	}
	
	/**
	 * @return bool
	 */
	public function commit()
	{
		return $this->connection->commit();
	}
	
	/**
	 * @return bool
	 */
	public function rollBack()
	{
		return $this->connection->rollBack();
	}

	public function query($query) {
		self::$nbrOfQuery++;
		return $this->connection->query($query);
	}
	
	public function prepare($query) {
		self::$nbrOfQuery++;
		return $this->connection->prepare($query);
	}
	
	/**
	 * @param string $query
	 * @return \PDOStatement
	 */
	public function exec($query)
	{
		self::$nbrOfQuery++;
		return $this->connection->exec($query);
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
