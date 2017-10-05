<?php

namespace Asylamba\Modules\Hephaistos\Repository;

use Asylamba\Modules\Hephaistos\Model\Charge;

class ChargeRepository extends TransactionRepository
{
    /**
     * @return array
     */
    public function getGlobalExpenses()
    {
        $statement = $this->connection->query(
            'SELECT c.category, SUM(t.amount) AS expense FROM budget__charges c
            INNER JOIN budget__transactions t ON c.transaction_id = t.id
            GROUP BY c.category'
        );
        $data = [];
        while ($row = $statement->fetch()) {
            $data[$row['category']] = (int) $row['expense'];
        }
        return $data;
    }
    
    /**
     * @return array
     */
    public function getMonthlyExpenses()
    {
        $statement = $this->connection->query(
            'SELECT c.category, SUM(t.amount) AS expense FROM budget__charges c
            INNER JOIN budget__transactions t ON c.transaction_id = t.id
            WHERE YEAR(t.created_at) = YEAR(CURRENT_DATE()) AND 
            MONTH(t.created_at) = MONTH(CURRENT_DATE())
            GROUP BY c.category'
        );
        $data = [];
        while ($row = $statement->fetch()) {
            $data[$row['category']] = (int) $row['expense'];
        }
        return $data;
    }
    
    public function insert($charge)
    {
        parent::insert($charge);
        
        $statement = $this->connection->prepare(
            'INSERT INTO budget__charges(transaction_id, category)
            VALUES(:transaction_id, :category)'
        );
        $statement->execute([
            'transaction_id' => $charge->getId(),
            'category' => $charge->getCategory()
        ]);
    }
    
    public function update($charge)
    {
        
    }
    
    public function remove($charge)
    {
        
    }
    
    public function format($data)
    {
        return
            (new Charge())
            ->setId((int) $data['id'])
            ->setCategory($data['category'])
            ->setAmount((int) $data['amount'])
            ->setCreatedAt((new \DateTime($data['created_at'])))
        ;
    }
}