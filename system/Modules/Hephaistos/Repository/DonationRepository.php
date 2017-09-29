<?php

namespace Asylamba\Modules\Hephaistos\Repository;

use Asylamba\Classes\Entity\AbstractRepository;

use Asylamba\Modules\Hephaistos\Model\Donation;
use Asylamba\Modules\Zeus\Model\Player;

class DonationRepository extends AbstractRepository
{
    /**
     * @param Player $player
     * @return int
     */
    public function getPlayerSum(Player $player)
    {
        $statement = $this->connection->prepare('SELECT SUM(amount) AS player_sum FROM budget__donations WHERE player_bind_key = :player_bind_key');
        $statement->execute(['player_bind_key' => $player->getBind()]);
        
        return (int) $statement->fetch()['player_sum'];
    }
    
    /**
     * @param Player $player
     * @return array
     */
    public function getPlayerDonations(Player $player)
    {
        $statement = $this->connection->prepare('SELECT * FROM budget__donations ORDER BY created_at DESC WHERE player_bind_key = :player_bind_key');
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
        $statement = $this->connection->query('SELECT * FROM budget__donations ORDER BY created_at DESC');
        
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
        $statement = $this->connection->prepare(
            'INSERT INTO budget__donations(player_bind_key, token, amount, created_at) VALUES(:player_bind_key, :token, :amount, :created_at)'
        );
        $statement->execute([
            'player_bind_key' => $donation->getPlayer()->getBind(),
            'token' => $donation->getToken(),
            'amount' => $donation->getAmount(),
            'created_at' => $donation->getCreatedAt()->format('Y-m-d H:i:s')
        ]);
        $donation->setId($this->connection->lastInsertId());
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
            ->setToken($data['token'])
            ->setAmount((int) $data['amount'])
            ->setCreatedAt(new \DateTime($data['created_at']))
        ;
    }
}