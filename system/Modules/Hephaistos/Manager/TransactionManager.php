<?php

namespace Asylamba\Modules\Hephaistos\Manager;

use Asylamba\Classes\Entity\EntityManager;

use Asylamba\Modules\Hephaistos\Model\Transaction;

class TransactionManager
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
    
    public function getTreasury()
    {
        return $this->entityManager->getRepository(Transaction::class)->getTreasury();
    }
}