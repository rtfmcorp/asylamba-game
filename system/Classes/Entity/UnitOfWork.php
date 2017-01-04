<?php

namespace Asylamba\Classes\Entity;

class UnitOfWork {
    /** @var array **/
    protected $entities = [];
    /** @var EntityManager **/
    protected $entityManager;
    
    // Entity is not committed yet
    const METADATA_STAGED = 'staged';
    // Entity is persisted in database
    const METADATA_PERSISTED = 'persisted';
    // Entity is persisted in database and will be removed
    const METADATA_REMOVED = 'removed';
    
    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    public function addObject($object)
    {
        $this->entities[get_class($object)][spl_object_hash($object)] = [
            'state' => self::METADATA_STAGED,
            'instance' => $object
        ];
    }
    
    /**
     * @param object $object
     */
    public function removeObject($object)
    {
        $this->entities[get_class($object)][spl_object_hash($object)]['state'] = self::METADATA_REMOVED;
    }
    
    public function flushAll()
    {
        foreach (array_keys($this->entities) as $entityClass) {
            $this->flushEntity($entityClass);
        }
    }
    
    /**
     * @param string $entityClass
     */
    public function flushEntity($entityClass)
    {
        // If the entity was never persisted, just return
        if (!isset($this->entities[$entityClass])) {
            return;
        }
        $repository = $this->entityManager->getRepository($className);
        foreach ($this->entities[$entityClass] as $entity) {
            $this->flushObject($repository, $entityClass, $entity['instance']);
        }
    }
    
    /**
     * @param AbstractRepository $repository
     * @param string $className
     * @param object $object
     */
    public function flushObject(AbstractRepository $repository, $className, $object)
    {
        $entity = &$this->entities[$className][spl_object_hash($object)];
        
        switch($entity['state']) {
            case self::METADATA_STAGED:
                $repository->insert($object);
                $entity['state'] = self::METADATA_PERSISTED;
                break;
            case self::METADATA_PERSISTED: $repository->update($object); break;
            case self::METADATA_REMOVED:
                $repository->remove($object);
                unset($entity);
                break;
        }
    }
    
    public function clearAll()
    {
        $this->entities = [];
    }
    
    /**
     * @param string $entityClass
     */
    public function clearEntity($entityClass)
    {
        unset($this->entities[$entityClass]);
    }
    
    /**
     * @param object $object
     */
    public function clearObject($object)
    {
        $className = get_class($object);
        // It is not mandatory to test the existence of the object SPL hash in the stack
        // the unset function won't cause errors if it does not exist
        if(isset($this->entities[$className])) {
            unset($this->entities[$className][spl_object_hash($object)]);
        }
    }
}