<?php

namespace App\Classes\Entity;

use App\Classes\Database\Database as Connection;

class EntityManager {
    protected Connection $connection;
    protected UnitOfWork $unitOfWork;
    protected array $repositories = [];

    public function __construct(Connection $connection) {
        $this->connection = $connection;
    }
    
    public function init()
    {
        $this->unitOfWork = new UnitOfWork($this);
    }
	
	public function beginTransaction(): bool
	{
		return $this->connection->beginTransaction();
	}
	
	public function inTransaction(): bool
	{
		return $this->connection->inTransaction();
	}
	
	public function commit(): bool
	{
		return $this->connection->commit();
	}
	
	public function rollBack(): bool
	{
		return $this->connection->rollBack();
	}
	
    public function getRepository(string $entityClass): AbstractRepository
    {
        if (!isset($this->repositories[$entityClass])) {
			$repositoryClass = str_replace('Model', 'Repository', $entityClass) . 'Repository';
            $this->repositories[$entityClass] = new $repositoryClass($this->connection, $this->unitOfWork);
        }
        return $this->repositories[$entityClass];
    }
    
    public function persist(object $entity): void
    {
        $this->unitOfWork->addObject($entity, UnitOfWork::METADATA_STAGED);
    }
    
    public function remove(object $entity): void
    {
        $this->unitOfWork->removeObject($entity);
    }
    
    public function flush(mixed $entity = null): void
    {
        switch(gettype($entity)) {
            case 'NULL':
                $this->unitOfWork->flushAll();
                break;
            case 'string':
                $this->unitOfWork->flushEntity($entity);
                break;
            case 'object':
                $className = get_class($entity);
                $this->unitOfWork->flushObject($this->getRepository($className), $className, $entity);
                break;
        }
    }
    
    public function clear(mixed $entity = null): void
    {
        switch(gettype($entity)) {
            case 'NULL':
                $this->unitOfWork->clearAll();
                break;
            case 'string':
                $this->unitOfWork->clearEntity($entity);
                break;
            case 'object':
                $this->unitOfWork->clearObject($entity);
                break;
        }
    }
    
    public function getUnitOfWork(): UnitOfWork
    {
        return $this->unitOfWork;
    }
	
	public function getConnection(): Connection
	{
		return $this->connection;
	}
}
