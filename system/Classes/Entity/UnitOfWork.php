<?php

namespace Asylamba\Classes\Entity;

use Asylamba\Classes\Entity\AbstractRepository;

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
    
    public function addObject($object, $state = self::METADATA_PERSISTED)
    {
		$identifier = ($object->getId() !== null) ? (int) $object->getId() : spl_object_hash($object);
		
        $this->entities[get_class($object)][$identifier] = [
            'state' => $state,
            'instance' => $object
        ];
    }
	
	public function hasObject($object)
	{
		return isset($this->entities[get_class($object)][(int) $object->getId()]);
	}
	
	public function getObject($entityClass, $id)
	{
		if (!isset($this->entities[$entityClass][(int) $id])) {
			return null;
		}
		return $this->entities[$entityClass][(int) $id]['instance'];
	}
    
    /**
     * @param object $object
     */
    public function removeObject($object)
    {
		if (!isset($this->entities[get_class($object)][$object->getId()]['instance'])) {
			return;
		}
        $this->entities[get_class($object)][$object->getId()]['state'] = self::METADATA_REMOVED;
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
        $repository = $this->entityManager->getRepository($entityClass);
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
		$identifier = ($object->getId() !== null) ? $object->getId() : spl_object_hash($object);
		if (!isset($this->entities[$className][$identifier])) {
			return false;
		}
        switch($this->entities[$className][$identifier]['state']) {
            case self::METADATA_STAGED:
                $repository->insert($object);
				unset($this->entities[$className][$identifier]);
				$this->entities[$className][(int) $object->getId()] = [
					'state' => self::METADATA_PERSISTED,
					'instance' => $object
				];
                break;
            case self::METADATA_PERSISTED: $repository->update($object); break;
            case self::METADATA_REMOVED:
                $repository->remove($object);
                unset($this->entities[$className][$identifier]);
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
    
    /**
     * @param string $repository
     * @return AbstractRepository
     */
    public function getRepository($repository)
    {
        return $this->entityManager->getRepository($repository);
    }
}