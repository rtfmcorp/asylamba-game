<?php

namespace Asylamba\Modules\Ares\Manager;

use Asylamba\Classes\Entity\EntityManager;

use Asylamba\Modules\Ares\Model\Commander;
use Asylamba\Modules\Ares\Model\Squadron;

class SquadronManager
{
    /** @var EntityManager **/
    protected $entityManager;
    
    /**
     * @param EntityManager $entityManager
     */
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }
    
    /**
     * @param Commander $commander
     * @return array
     */
    public function getCommanderSquadrons(Commander $commander)
    {
        return $this->entityManager->getRepository(Squadron::class)->getCommanderSquadrons($commander->getId());
    }
    
    /**
     * @param int $id
     * @return Squadron
     */
    public function get($id)
    {
        return $this->entityManager->getRepository(Squadron::class)->get($id);
    }
}
