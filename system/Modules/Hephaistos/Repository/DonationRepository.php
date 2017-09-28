<?php

namespace Asylamba\Modules\Hephaistos\Repository;

use Asylamba\Classes\Entity\AbstractRepository;

use Asylamba\Modules\Hephaistos\Model\Donation;
use Asylamba\Modules\Zeus\Model\Player;

class DonationRepository extends AbstractRepository
{
    /**
     * @param Player $player
     * @return array
     */
    public function getPlayerCharges(Player $player)
    {
        $statement = $this->connection->prepare('SELECT * FROM budget__donations ORDER BY created_at DESC WHERE player_id = :player_id');
        $statement->execute(['player_id' => $player->getId()]);
        
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
    public function getAllCharges()
    {
        $statement = $this->connection->exec('SELECT * FROM budget__donations ORDER BY created_at DESC');
        
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
            'INSERT INTO budget__donations(player_id, token, amount, created_at) VALUES(:player_id, :token, :amount, :created_at)'
        );
        $statement->execute([
            'player_id' => $donation->getPlayer()->getId(),
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
            ->setToken($data['token'])
            ->setAmount((int) $data['amount'])
            ->setCreatedAt(new \DateTime($data['created_at']))
        ;
    }
}