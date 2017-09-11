<?php

namespace Asylamba\Classes\Entity;

use Asylamba\Classes\Database\Database as Connection;

class EntityManager
{
    /** @var Connection **/
    protected $connection;
    /** @var UnitOfWork **/
    protected $unitOfWork;
    /** @var array **/
    protected $repositories;
    
    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }
    
    public function init()
    {
        $this->unitOfWork = new UnitOfWork($this);
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
    
    /**
     * @param string $entityClass
     * @return AbstractRepository
     */
    public function getRepository($entityClass)
    {
        if (!isset($this->repositories[$entityClass])) {
            $repositoryClass = str_replace('Model', 'Repository', $entityClass) . 'Repository';
            $this->repositories[$entityClass] = new $repositoryClass($this->connection, $this->unitOfWork);
        }
        return $this->repositories[$entityClass];
    }
    
    /**
     * @param object $entity
     */
    public function persist($entity)
    {
        $this->unitOfWork->addObject($entity, UnitOfWork::METADATA_STAGED);
    }
    
    /**
     * @param object $entity
     */
    public function remove($entity)
    {
        $this->unitOfWork->removeObject($entity);
    }
    
    /**
     * @param mixed $entity
     */
    public function flush($entity = null)
    {
        switch (gettype($entity)) {
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
    
    /**
     * @param mixed $entity
     */
    public function clear($entity = null)
    {
        switch (gettype($entity)) {
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
    
    /**
     * @return UnitOfWork
     */
    public function getUnitOfWork()
    {
        return $this->unitOfWork;
    }
    
    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }
}
