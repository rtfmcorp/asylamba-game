<?php

namespace Asylamba\Modules\Hermes\Model\Press;

use Asylamba\Modules\Athena\Model\Transaction;

class TradeNews extends News
{
    /** @var Transaction **/
    protected $transaction;
    
    /**
     * @param Transaction $transaction
     * @return $this
     */
    public function setTransaction(Transaction $transaction)
    {
        $this->transaction = $transaction;
        
        return $this;
    }
    
    /**
     * @return Transaction
     */
    public function getTransaction()
    {
        return $this->transaction;
    }
}
