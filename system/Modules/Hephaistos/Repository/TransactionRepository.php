<?php

namespace Asylamba\Modules\Hephaistos\Repository;

use Asylamba\Classes\Entity\AbstractRepository;

use Asylamba\Modules\Hephaistos\Model\Charge;
use Asylamba\Modules\Hephaistos\Model\Donation;
use Asylamba\Modules\Hephaistos\Model\Transaction;

class TransactionRepository extends AbstractRepository
{
    public function getTreasury()
    {
        return $this->connection->query(
            'SELECT SUM(amount) AS treasury, MONTH(created_at) AS month FROM budget__transactions 
            WHERE created_at > DATE(DATE_ADD(created_at, INTERVAL -5 MONTH))
            GROUP BY MONTH(created_at)'
        )->fetchAll();
    }
    
    public function insert($transaction)
    {
        $statement = $this->connection->prepare(
            'INSERT INTO budget__transactions(transaction_type, amount, created_at) VALUES(:transaction_type, :amount, :created_at)'
        );
        $statement->execute([
            'transaction_type' => $transaction->getTransactionType(),
            'amount' => $transaction->getAmount(),
            'created_at' => $transaction->getCreatedAt()->format('Y-m-d H:i:s')
        ]);
        $transaction->setId($this->connection->lastInsertId());
    }
    
    public function update($entity)
    {
        
    }
    
    public function remove($entity)
    {
        
    }
    
    public function format($data)
    {
        return $this
            ->unitOfWork
            ->getRepository(
                (($data['transaction_type'] === Transaction::TYPE_DONATION) ? Donation::class : Charge::class)
            )->format($data)
        ;
    }
}