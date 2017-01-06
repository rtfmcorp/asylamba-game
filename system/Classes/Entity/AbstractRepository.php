<?php

namespace Asylamba\Classes\Entity;

use Asylamba\Classes\Database\Database as Connection;
use Asylamba\Classes\Entity\UnitOfWork;

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
}