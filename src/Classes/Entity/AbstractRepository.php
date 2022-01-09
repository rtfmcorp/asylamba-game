<?php

namespace App\Classes\Entity;

use App\Classes\Database\Database as Connection;
use App\Classes\Entity\UnitOfWork;

abstract class AbstractRepository {
    /** @var Connection **/
    protected $connection;
	/** @var UnitOfWork **/
	protected $unitOfWork;
	
	/**
	 * @param Connection $connection
	 * @param UnitOfWork $unitOfWork
	 */
	public function __construct(Connection $connection, UnitOfWork $unitOfWork)
	{
		$this->connection = $connection;
		$this->unitOfWork = $unitOfWork;
	}
    
    /**
     * @param object $entity
     */
    abstract public function insert($entity);
    
    /**
     * @param object $entity
     */
    abstract public function update($entity);
    
    /**
     * @param object $entity
     */
    abstract public function remove($entity);
	
	/**
	 * @param array $orderBy
	 * @return string
	 */
	protected function getOrderByClause($orderBy = [])
	{
		if(empty($orderBy)) {
			return;
		}
		$clause = 'ORDER BY ';
		foreach ($orderBy as $column => $order) {
			$clause .= "$column $order,";
		}
		return substr($clause, 0, -1);
	}
}
