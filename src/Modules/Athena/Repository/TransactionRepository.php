<?php

namespace App\Modules\Athena\Repository;

use App\Classes\Entity\AbstractRepository;

use App\Modules\Athena\Model\Transaction;

class TransactionRepository extends AbstractRepository
{
    protected function select($clause = '', $parameters = [])
    {
        $statement = $this->connection->prepare(
            'SELECT t.*,
			play.name AS playerName,
			play.rColor AS playerColor,
			ob.name AS placeName,
			s.rSector AS sector,
			se.rColor AS sectorColor,
			p.rSystem AS rSystem,
			p.position AS positionInSystem,
			s.xPosition AS xSystem,
			s.yPosition AS ySystem,
			c.name AS commanderName,
			c.level AS commanderLevel, 
			c.palmares AS commanderVictory,
			c.experience AS commanderExperience,
			c.avatar as commanderAvatar
			FROM transaction AS t
			LEFT JOIN player AS play ON t.rPlayer = play.id
			LEFT JOIN orbitalBase AS ob ON t.rPlace = ob.rPlace
			LEFT JOIN place AS p ON t.rPlace = p.id
			LEFT JOIN system AS s ON p.rSystem = s.id
			LEFT JOIN sector AS se ON s.rSector = se.id
			LEFT JOIN commander AS c ON t.identifier = c.id
			' . $clause
        );
        $statement->execute($parameters);
        return $statement;
    }
    
    public function get($id)
    {
		if (($t = $this->unitOfWork->getObject(Transaction::class, $id)) !== null) {
			return $t;
		}
		$query = $this->select('WHERE t.id = :id', ['id' => $id]);
		
		if (($row = $query->fetch()) === false) {
			return null;
		}
		$transaction = $this->format($row);
		$this->unitOfWork->addObject($transaction);
		return $transaction;
    }
    
    /**
     * @param int $type
     * @param int $statement
     * @param int $limit
     * @return array
     */
    public function getLastCompletedTransaction($type)
    {
		$query = $this->select('WHERE t.type = :type AND t.statement = ' . Transaction::ST_COMPLETED . ' ORDER BY t.dValidation DESC LIMIT 1', [
            'type' => $type
        ]);
		if (($row = $query->fetch()) === false) {
            return null;
        }
        if (($t = $this->unitOfWork->getObject(Transaction::class, $row['id'])) !== null) {
            return $t;
        }
        $transaction = $this->format($row);
        $this->unitOfWork->addObject($transaction);
        return $transaction;
    }
    
	/**
	 * @param int $type
	 * @return array
	 */
	public function getProposedTransactions($type)
	{
		$query = $this->select('WHERE t.type = :type AND t.statement = ' . Transaction::ST_PROPOSED . ' ORDER BY t.dPublication DESC LIMIT 20', [
            'type' => $type
        ]);
		
		$data = [];
		while($row = $query->fetch()) {
			if (($t = $this->unitOfWork->getObject(Transaction::class, $row['id'])) !== null) {
				$data[] = $t;
				continue;
			}
			$transaction = $this->format($row);
			$this->unitOfWork->addObject($transaction);
			$data[] = $transaction;
		}
		return $data;
	}
    
	/**
     * @param int $playerId
	 * @param int $type
	 * @return array
	 */
	public function getPlayerPropositions($playerId, $type)
	{
		$query = $this->select('WHERE t.rPlayer = :player_id AND t.type = :type AND t.statement = ' . Transaction::ST_PROPOSED, [
            'player_id' => $playerId,
            'type' => $type
        ]);
		
		$data = [];
		while($row = $query->fetch()) {
			if (($t = $this->unitOfWork->getObject(Transaction::class, $row['id'])) !== null) {
				$data[] = $t;
				continue;
			}
			$transaction = $this->format($row);
			$this->unitOfWork->addObject($transaction);
			$data[] = $transaction;
		}
		return $data;
	}
    
	/**
     * @param int $placeId
	 * @return array
	 */
	public function getBasePropositions($placeId)
	{
		$query = $this->select('WHERE t.rPlace = :place_id AND t.statement = ' . Transaction::ST_PROPOSED, [
            'place_id' => $placeId
        ]);
		
		$data = [];
		while($row = $query->fetch()) {
			if (($t = $this->unitOfWork->getObject(Transaction::class, $row['id'])) !== null) {
				$data[] = $t;
				continue;
			}
			$transaction = $this->format($row);
			$this->unitOfWork->addObject($transaction);
			$data[] = $transaction;
		}
		return $data;
	}

    /**
     * @param int $transactionType
     * @return mixed
     */
	public function getExchangeRate($transactionType) {
		$statement = $this->connection->prepare(
            'SELECT currentRate
			FROM transaction 
			WHERE type = ? AND statement = ?
			ORDER BY dValidation DESC 
			LIMIT 1'
        );
		$statement->execute(array($transactionType, Transaction::ST_COMPLETED));
		return $statement->fetch()['currentRate'];
	}
    
    public function insert($transaction)
    {
		$statement = $this->connection->prepare('INSERT INTO
			transaction(rPlayer, rPlace, type, quantity, identifier, price, commercialShipQuantity, statement, dPublication, dValidation, currentRate)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
		$statement->execute(array(
			$transaction->rPlayer,
			$transaction->rPlace,
			$transaction->type,
			$transaction->quantity,
			$transaction->identifier,
			$transaction->price,
			$transaction->commercialShipQuantity,
			$transaction->statement,
			$transaction->dPublication,
			$transaction->dValidation,
			$transaction->currentRate
		));
		$transaction->id = $this->connection->lastInsertId();
    }
    
    public function update($transaction)
    {
        $statement = $this->connection->prepare(
            'UPDATE transaction
            SET	id = ?,
                rPlayer = ?,
                rPlace = ?,
                type = ?,
                quantity = ?,
                identifier = ?,
                price = ?,
                commercialShipQuantity = ?,
                statement = ?,
                dPublication = ?,
                dValidation = ?,
                currentRate = ?
            WHERE id = ?');
        $statement->execute(array(
            $transaction->id,
            $transaction->rPlayer,
            $transaction->rPlace,
            $transaction->type,
            $transaction->quantity,
            $transaction->identifier,
            $transaction->price,
            $transaction->commercialShipQuantity,
            $transaction->statement,
            $transaction->dPublication,
            $transaction->dValidation,
            $transaction->currentRate,
            $transaction->id
        ));
    }
    
    public function remove($transaction)
    {
		$statement = $this->connection->prepare('DELETE FROM transaction WHERE id = ?');
		$statement->execute(array($transaction->id));
    }
    
    /**
     * @param array $data
     * @return Transaction
     */
    public function format($data)
    {
        $transaction = new Transaction();

        $transaction->id = (int) $data['id'];
        $transaction->rPlayer = (int) $data['rPlayer'];
        $transaction->rPlace = (int) $data['rPlace'];
        $transaction->type = $data['type'];
        $transaction->quantity = $data['quantity'];
        $transaction->identifier = $data['identifier'];
        $transaction->price = $data['price'];
        $transaction->shipQuantity = $data['commercialShipQuantity'];
        $transaction->statement = $data['statement'];
        $transaction->dPublication = $data['dPublication'];
        $transaction->dValidation = $data['dValidation'];
        $transaction->currentRate = $data['currentRate'];

        $transaction->playerName = $data['playerName'];
        $transaction->playerColor = $data['playerColor'];
        $transaction->placeName = $data['placeName'];
        $transaction->sector = $data['sector'];
        $transaction->sectorColor = $data['sectorColor'];
        $transaction->rSystem = $data['rSystem'];
        $transaction->positionInSystem = $data['positionInSystem'];
        $transaction->xSystem = $data['xSystem'];
        $transaction->ySystem = $data['ySystem'];

        $transaction->commanderName = $data['commanderName'];
        $transaction->commanderLevel = $data['commanderLevel'];
        $transaction->commanderVictory = $data['commanderVictory'];
        $transaction->commanderExperience = $data['commanderExperience'];
        $transaction->commanderAvatar = $data['commanderAvatar'];
        
        return $transaction;
    }
}
