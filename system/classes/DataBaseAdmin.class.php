<?php
class DataBaseAdmin {
	private $PDOInstance = null;
	private static $instance = null;
	private static $nbrOfQuery = 0;
	private static $nbrOfInstance = 0;

	# gil const
	const DEFAULT_SQL_USER = 'root';
	const DEFAULT_SQL_HOST = '127.0.0.1';
	const DEFAULT_SQL_PASS = '';
	const DEFAULT_SQL_DTB  = 'expansion_s2';
	
	public static function getNbrOfQuery() {
		return self::$nbrOfQuery;
	}
	public static function getNbrOfInstance() {
		return self::$nbrOfInstance;
	}

	private function __construct() {
		try {
			$pdoOptions[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
			$pdoOptions[PDO::ATTR_EMULATE_PREPARES] = FALSE;
			# $pdoOptions[PDO::ATTR_DEFAULT_FETCH_MODE] = PDO::FETCH_ASSOC;
			$this->PDOInstance = new PDO(
				'mysql:dbname=' . DataBaseAdmin::DEFAULT_SQL_DTB . ';host=' . DataBaseAdmin::DEFAULT_SQL_HOST . ';charset=utf8', 
				DataBaseAdmin::DEFAULT_SQL_USER, 
				DataBaseAdmin::DEFAULT_SQL_PASS, 
				$pdoOptions
			);
		} catch (PDOException $e) {
			echo 'Erreur de connection à la base de donnée : ' . $e->getMessage();
			exit();
		}
	}

	public static function getInstance() {
		if (is_null(self::$instance)) {
			self::$instance = new DataBaseAdmin();
			self::$nbrOfInstance++;
		}
		return self::$instance;
	}
	
	public function query($query) {
		self::$nbrOfQuery++;
		return $this->PDOInstance->query($query);
	}
	public function prepare($query) {
		return $this->PDOInstance->prepare($query);
	}
	public function execute($query) {
		self::$nbrOfQuery++;
		return $this->PDOInstance->exec($query);
	}
	public function lastInsertId() {
		return $this->PDOInstance->lastInsertId();
	}
	public function quote($query) {
		return $this->PDOInstance->quote($query);
	}
}