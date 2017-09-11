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
    
    public function getNewsPicto()
    {
        switch ($this->transaction->type) {
            case Transaction::TYP_RESOURCE:
                return MEDIA . 'market/resources-pack-' . Transaction::getResourcesIcon($this->transaction->quantity) . '.png';
            case Transaction::TYP_SHIP:
                return MEDIA . 'ship/picto/ship' . $this->transaction->identifier . '.png';
            case Transaction::TYP_COMMANDER:
                return MEDIA . 'commander/small/' . $this->transaction->commanderAvatar . '.png';
        }
    }
    
    /**
     * {@inheritdoc}
     */
    protected function getNewsType()
    {
        return self::NEWS_TYPE_TRADE;
    }
    
    public function jsonSerialize()
    {
        return array_merge(parent::jsonSerialize(), [
            'transaction' => $this->transaction
        ]);
    }
}
