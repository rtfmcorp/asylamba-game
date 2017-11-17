<?php

namespace Asylamba\Modules\Hephaistos\Repository;

use Asylamba\Modules\Hephaistos\Model\Donation;
use Asylamba\Modules\Zeus\Model\Player;

class DonationRepository extends TransactionRepository
{
    /**
     * @return int
     */
    public function getMonthlyIncome()
    {
        return (int) $this->connection->query(
            'SELECT SUM(amount) AS income FROM budget__transactions 
            WHERE transaction_type = "' . Donation::TYPE_DONATION . '" AND YEAR(created_at) = YEAR(CURRENT_DATE()) AND 
            MONTH(created_at) = MONTH(CURRENT_DATE())'
        )->fetch()['income'];
    }
    
    /**
     * @return int
     */
    public function getGlobalIncome()
    {
        return (int) $this->connection->query(
            'SELECT SUM(amount) AS income FROM budget__transactions 
            WHERE transaction_type = "' . Donation::TYPE_DONATION . '"'
        )->fetch()['income'];
    }
    
    /**
     * @param Player $player
     * @return int
     */
    public function getPlayerSum(Player $player)
    {
        $statement = $this->connection->prepare(
            'SELECT SUM(t.amount) AS player_sum FROM budget__donations d
            INNER JOIN budget__transactions t ON t.id = d.transaction_id
            WHERE d.player_bind_key = :player_bind_key'
        );
        $statement->execute(['player_bind_key' => $player->getBind()]);
        
        return (int) $statement->fetch()['player_sum'];
    }
    
    /**
     * @param Player $player
     * @return array
     */
    public function getPlayerDonations(Player $player)
    {
        $statement = $this->connection->prepare(
            'SELECT * FROM budget__donations d INNER JOIN budget__transactions t ON t.id = d.transaction_id
            ORDER BY t.created_at DESC WHERE d.player_bind_key = :player_bind_key'
        );
        $statement->execute(['player_bind_key' => $player->getBind()]);
        
        $data = [];
        while ($row = $statement->fetch()) {
            if (($d = $this->unitOfWork->getObject(Donation::class, $row['id'])) !== null) {
                $data[] = $d;
                continue;
            }
            $donation = $this->format($row);
            $this->unitOfWork->addObject($donation);
            $data[] = $donation;
        }
        return $data;
    }
    
    /**
     * @return array
     */
    public function getAllDonations()
    {
        $statement = $this->connection->query(
            'SELECT * FROM budget__donations d INNER JOIN budget__transactions t ON t.id = d.transaction_id
            ORDER BY t.created_at DESC'
        );
        $data = [];
        while ($row = $statement->fetch()) {
            if (($d = $this->unitOfWork->getObject(Donation::class, $row['id'])) !== null) {
                $data[] = $d;
                continue;
            }
            $donation = $this->format($row);
            $this->unitOfWork->addObject($donation);
            $data[] = $donation;
        }
        return $data;
    }
    
    public function insert($donation)
    {
        parent::insert($donation);
        
        $statement = $this->connection->prepare(
            'INSERT INTO budget__donations(transaction_id, player_bind_key, token)
            VALUES(:transaction_id, :player_bind_key, :token)'
        );
        $statement->execute([
            'transaction_id' => $donation->getId(),
            'player_bind_key' => $donation->getPlayer()->getBind(),
            'token' => $donation->getToken()
        ]);
    }
    
    public function update($donation)
    {
        
    }
    
    public function remove($donation)
    {
        
    }
    
    public function format($data)
    {
        return
            (new Donation())
            ->setId((int) $data['id'])
            ->setPlayer($this->unitOfWork->getRepository(Player::class)->getByBindKey($data['player_bind_key']))
            ->setToken($data['token'])
            ->setAmount((int) $data['amount'])
            ->setCreatedAt(new \DateTime($data['created_at']))
        ;
    }
}