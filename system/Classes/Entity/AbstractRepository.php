<?php

namespace Asylamba\Classes\Entity;

use Asylamba\Classes\Database\Database as Connection;

abstract class AbstractRepository {
    /** @var Connection **/
    protected $connection;
    
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