<?php

namespace Asylamba\Modules\Hephaistos\Model;

class Charge extends Transaction
{
    /** @var string **/
    protected $category;
    
    const CATEGORY_STRIPE = 'stripe';
    const CATEGORY_SERVER = 'server';
    
    /**
     * @param string $category
     * @return $this
     */
    public function setCategory($category)
    {
        $this->category = $category;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getTransactionType()
    {
        return self::TYPE_CHARGE;
    }
    
    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'transaction_type' => $this->getTransactionType(),
            'category' => $this->category,
            'created_at' => $this->createdAt
            
        ];
    }
}