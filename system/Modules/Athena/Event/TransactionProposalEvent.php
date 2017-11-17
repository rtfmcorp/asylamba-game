<?php

namespace Asylamba\Modules\Athena\Event;

use Asylamba\Modules\Athena\Model\Transaction;

class TransactionProposalEvent
{
    /** @var Transaction **/
    protected $transaction;
    
    const NAME = 'athena.transaction_proposal';
    
    /**
     * @param Transaction $transaction
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }
    
    /**
     * @return Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}
