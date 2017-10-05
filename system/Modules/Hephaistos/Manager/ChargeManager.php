<?php

namespace Asylamba\Modules\Hephaistos\Manager;

use Asylamba\Classes\Entity\EntityManager;

use Asylamba\Modules\Hephaistos\Model\Charge;

class ChargeManager
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
     * @return array
     */
    public function getGlobalExpenses()
    {
        return $this->entityManager->getRepository(Charge::class)->getGlobalExpenses();
    }
    
    /**
     * @return array
     */
    public function getMonthlyExpenses()
    {
        return $this->entityManager->getRepository(Charge::class)->getMonthlyExpenses();
    }
}