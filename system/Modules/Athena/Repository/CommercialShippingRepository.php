<?php

namespace Asylamba\Modules\Athena\Repository;

use Asylamba\Classes\Entity\AbstractRepository;

use Asylamba\Modules\Athena\Model\CommercialShipping;

class CommercialShippingRepository extends AbstractRepository
{
    public function select($clause = '', $params = [])
    {
        $statement = $this->connection->prepare(
            'SELECT cs.*, 
			p1.rSystem AS rSystem1, p1.position AS position1, s1.xPosition AS xSystem1, s1.yPosition AS ySystem1,
			p2.rSystem AS rSystem2, p2.position AS position2, s2.xPosition AS xSystem2, s2.yPosition AS ySystem2,
			t.type AS typeOfTransaction, t.quantity AS quantity, t.identifier AS identifier, t.price AS price,
			c.avatar AS commanderAvatar, c.name AS commanderName, c.level AS commanderLevel, c.palmares AS commanderVictory, c.experience AS commanderExperience
			FROM commercialShipping AS cs
			LEFT JOIN place AS p1 
				ON cs.rBase = p1.id
			LEFT JOIN system AS s1 
				ON p1.rSystem = s1.id
			LEFT JOIN place AS p2 
				ON cs.rBaseDestination = p2.id 
			LEFT JOIN system AS s2 
				ON p2.rSystem = s2.id 
			LEFT JOIN transaction AS t 
				ON cs.rTransaction = t.id
			LEFT JOIN commander AS c 
				ON t.identifier = c.id ' . $clause
        );
        $statement->execute($params);
        
        return $statement;
    }
    
    /**
     * @param int $id
     * @return CommercialShipping
     */
    public function get($id)
    {
        if (($cs = $this->unitOfWork->getObject(CommercialShipping::class, $id)) !== null) {
            return $cs;
        }
        $query = $this->select('WHERE cs.id = :id', ['id' => $id]);
        
        if (($row = $query->fetch()) === false) {
            return null;
        }
        $commercialShipping = $this->format($row);
        $this->unitOfWork->addObject($commercialShipping);
        return $commercialShipping;
    }
    
    /**
     * @param int $id
     * @return CommercialShipping
     */
    public function getByTransactionId($id)
    {
        $query = $this->select('WHERE cs.rTransaction = :transaction_id', ['transaction_id' => $id]);
        
        if (($row = $query->fetch()) === false) {
            return null;
        }
        if (($cs = $this->unitOfWork->getObject(CommercialShipping::class, (int) $row['id'])) !== null) {
            return $cs;
        }
        $commercialShipping = $this->format($row);
        $this->unitOfWork->addObject($commercialShipping);
        return $commercialShipping;
    }
    
    /**
     * @return array
     */
    public function getAll()
    {
        $query = $this->select();
        
        $data = [];
        while ($row = $query->fetch()) {
            if (($cs = $this->unitOfWork->getObject(CommercialShipping::class, $row['id'])) !== null) {
                $data[] = $cs;
                continue;
            }
            $commercialShipping = $this->format($row);
            $this->unitOfWork->addObject($commercialShipping);
            $data[] = $commercialShipping;
        }
        return $data;
    }
    
    /**
     * @param int $orbitalBaseId
     * @return array
     */
    public function getByBase($orbitalBaseId)
    {
        $query = $this->select('WHERE cs.rBase = :base_id OR cs.rBaseDestination = :destination_base_id', [
            'base_id' => $orbitalBaseId,
            'destination_base_id' => $orbitalBaseId
        ]);
        
        $data = [];
        while ($row = $query->fetch()) {
            if (($cs = $this->unitOfWork->getObject(CommercialShipping::class, $row['id'])) !== null) {
                $data[] = $cs;
                continue;
            }
            $commercialShipping = $this->format($row);
            $this->unitOfWork->addObject($commercialShipping);
            $data[] = $commercialShipping;
        }
        return $data;
    }
    
    /**
     * @param CommercialShipping $commercialShipping
     */
    public function insert($commercialShipping)
    {
        $qr = $this->connection->prepare('INSERT INTO
			commercialShipping(rPlayer, rBase, rBaseDestination, rTransaction, resourceTransported, shipQuantity, dDeparture, dArrival, statement)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $qr->execute(array(
            $commercialShipping->rPlayer,
            $commercialShipping->rBase,
            $commercialShipping->rBaseDestination,
            $commercialShipping->rTransaction,
            $commercialShipping->resourceTransported,
            $commercialShipping->shipQuantity,
            $commercialShipping->dDeparture,
            $commercialShipping->dArrival,
            $commercialShipping->statement
        ));
        $commercialShipping->id = $this->connection->lastInsertId();
    }
    
    /**
     * @param CommercialShipping $commercialShipping
     */
    public function update($commercialShipping)
    {
        $statement = $this->connection->prepare(
            'UPDATE commercialShipping
			SET	id = ?,
				rPlayer = ?,
				rBase = ?,
				rBaseDestination = ?,
				rTransaction = ?,
				resourceTransported = ?,
				shipQuantity = ?,
				dDeparture = ?,
				dArrival = ?,
				statement = ?
			WHERE id = ?'
        );
        $statement->execute(array(
            $commercialShipping->id,
            $commercialShipping->rPlayer,
            $commercialShipping->rBase,
            $commercialShipping->rBaseDestination,
            $commercialShipping->rTransaction,
            $commercialShipping->resourceTransported,
            $commercialShipping->shipQuantity,
            $commercialShipping->dDeparture,
            $commercialShipping->dArrival,
            $commercialShipping->statement,
            $commercialShipping->id
        ));
    }
    
    /**
     * @param CommercialShipping $commercialShipping
     */
    public function remove($commercialShipping)
    {
        $statement = $this->connection->prepare('DELETE FROM commercialShipping WHERE id = :id');
        $statement->execute(['id' => $commercialShipping->id]);
    }
    
    /**
     * @param array $data
     * @return CommercialShipping
     */
    public function format($data)
    {
        $commercialShipping = new CommercialShipping();

        $commercialShipping->id = (int) $data['id'];
        $commercialShipping->rPlayer = (int) $data['rPlayer'];
        $commercialShipping->rBase = (int) $data['rBase'];
        $commercialShipping->rBaseDestination = (int) $data['rBaseDestination'];
        $commercialShipping->rTransaction = (int) $data['rTransaction'];
        $commercialShipping->resourceTransported = (int) $data['resourceTransported'];
        $commercialShipping->shipQuantity = (int) $data['shipQuantity'];
        $commercialShipping->dDeparture = $data['dDeparture'];
        $commercialShipping->dArrival = $data['dArrival'];
        $commercialShipping->statement = (int) $data['statement'];

        $commercialShipping->price = (int) $data['price'];

        $commercialShipping->baseRSystem = (int) $data['rSystem1'];
        $commercialShipping->basePosition = $data['position1'];
        $commercialShipping->baseXSystem = $data['xSystem1'];
        $commercialShipping->baseYSystem = $data['ySystem1'];

        $commercialShipping->destinationRSystem = (int) $data['rSystem2'];
        $commercialShipping->destinationPosition = $data['position2'];
        $commercialShipping->destinationXSystem = $data['xSystem2'];
        $commercialShipping->destinationYSystem = $data['ySystem2'];

        $commercialShipping->typeOfTransaction = $data['typeOfTransaction'];
        $commercialShipping->quantity = (int) $data['quantity'];
        $commercialShipping->identifier = $data['identifier'];
        $commercialShipping->commanderAvatar = $data['commanderAvatar'];
        $commercialShipping->commanderName = $data['commanderName'];
        $commercialShipping->commanderLevel = (int) $data['commanderLevel'];
        $commercialShipping->commanderVictory = (int) $data['commanderVictory'];
        $commercialShipping->commanderExperience = (int) $data['commanderExperience'];
        
        return $commercialShipping;
    }
}
