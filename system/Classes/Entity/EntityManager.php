<?php

namespace Asylamba\Classes\Entity;

use Asylamba\Classes\Database\Database as Connection;

class EntityManager {
    /** @var Connection **/
    protected $connection;
    /** @var UnitOfWork **/
    protected $unitOfWork;
    /** @var array **/
    protected $repositories;
    
    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection) {
        $this->connection = $connection;
    }
    
    public function init()
    {
        $this->unitOfWork = new UnitOfWork();
    }
    
    public function getRepository($entityClass)
    {
        if (!isset($this->repositories[$entityClass])) {
            $this->repositories[$entityClass] = new $repositoryClass($this->connection);
        }
        return $this->repositories[$entityClass];
    }
    
    /**
     * @param object $entity
     */
    public function persist($entity)
    {
        $this->unitOfWork->addObject($entity);
    }
    
    /**
     * @param object $entity
     */
    public function remove($entity)
    {
        $this->unitOfWork->removeObject($entity);
    }
    
    public function flush($entity = null)
    {
        switch(gettype($entity)) {
            case 'NULL':
                $this->unitOfWork->flushAll();
                break;
            case 'string':
                $this->unitOfWork->flushEntity(get_class($entity));
                break;
            case 'object':
                $className = get_class($entity);
                $this->unitOfWork->flushObject($this->getRepository($className), $className, $entity);
                break;
        }
    }
    
    public function clear($entity = null)
    {
        switch(gettype($entity)) {
            case 'NULL':
                $this->unitOfWork->clearAll();
                break;
            case 'string':
                $this->unitOfWork->clearEntity(get_class($entity));
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
}