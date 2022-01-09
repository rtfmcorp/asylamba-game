<?php

namespace App\Modules\Ares\Repository;

use App\Classes\Entity\AbstractRepository;
use App\Classes\Library\Utils;

use App\Modules\Ares\Model\Commander;

class CommanderRepository extends AbstractRepository {
	/**
	 * @param string $clause
	 * @param array $params
	 * @return \PDOStatement
	 */
	public function select($clause, $params = [])
	{
		$statement = $this->connection->prepare(
			'SELECT c.*,
				o.iSchool, o.name AS oName,
				p.name AS pName,
				p.rColor AS pColor,
				pd.population AS destinationPlacePop,
				ps.population AS startPlacePop,
				dp.name AS dpName,
				sp.name AS spName,
				sq.id AS sqId,
				sq.ship0 AS sqShip0,
				sq.ship1 AS sqShip1,
				sq.ship2 AS sqShip2,
				sq.ship3 AS sqShip3,
				sq.ship4 AS sqShip4,
				sq.ship5 AS sqShip5,
				sq.ship6 AS sqShip6,
				sq.ship7 AS sqShip7,
				sq.ship8 AS sqShip8,
				sq.ship9 AS sqShip9,
				sq.ship10 AS sqShip10,
				sq.ship11 AS sqShip11,
				sq.dCreation AS sqDCreation,
				sq.DLastModification AS sqDLastModification
			FROM commander AS c
			LEFT JOIN orbitalBase AS o ON o.rPlace = c.rBase
			LEFT JOIN player AS p ON p.id = c.rPlayer
			LEFT JOIN orbitalBase AS dp ON dp.rPlace = c.rDestinationPlace
			LEFT JOIN place AS pd ON pd.id = c.rDestinationPlace
			LEFT JOIN orbitalBase AS sp ON sp.rPlace = c.rStartPlace
			LEFT JOIN place AS ps ON ps.id = c.rStartPlace
			LEFT JOIN squadron AS sq ON sq.rCommander = c.id
			' . $clause
		);
		$statement->execute($params);
		return $statement;
	}
	
	/**
	 * 
	 * @param int $id
	 * @return Commander
	 */
	public function get($id)
	{
		if (($c = $this->unitOfWork->getObject(Commander::class, $id)) !== null) {
			return $c;
		}
		$statement = $this->select('WHERE c.id = :id', ['id' => $id]);
		if ($statement->rowCount() === 0) {
			return null;
		}
		$commanders = [];
		$currentId = 0;
		while ($row = $statement->fetch()) {
			$this->format($row, $commanders, $currentId);
		}
		return $commanders[$currentId];
	}
	
	/**
	 * @param int $baseId
	 * @param array $statements
	 * @param array $orderBy
	 * @return array
	 */
	public function getBaseCommanders($baseId, $statements = [], $orderBy = [])
	{
		$statementClause = (!empty($statements)) ? ' AND c.statement IN (' . implode(',', $statements) . ') ' : '';
		$statement = $this->select('WHERE c.rBase = :base_id ' . $statementClause . $this->getOrderByClause($orderBy), ['base_id' => $baseId]);
		$commanders = [];
		$currentId = 0;
		$persisted = [];
		$unPersisted = [];
		while ($row = $statement->fetch()) {
			if (in_array($row['id'], $persisted)) {
				continue;
			}
			if (!in_array($row['id'], $unPersisted) && ($c = $this->unitOfWork->getObject(Commander::class, $row['id'])) !== null) {
				$currentId = $row['id'];
				$commanders[$row['id']] = $c;
				$persisted[] = $row['id'];
				continue;
			}
			$this->format($row, $commanders, $currentId, $unPersisted);
		}
		return array_values($commanders);
	}
	
	/**
	 * @param array $statements
	 * @return array
	 */
	public function getAllByStatements($statements)
	{
		$statement = $this->select('WHERE c.statement IN (' . implode(',', $statements) . ')');
		$commanders = [];
		$currentId = 0;
		$persisted = [];
		$unPersisted = [];
		while ($row = $statement->fetch()) {
			if (in_array($row['id'], $persisted)) {
				continue;
			}
			if (!in_array($row['id'], $unPersisted) && ($c = $this->unitOfWork->getObject(Commander::class, $row['id'])) !== null) {
				$currentId = $row['id'];
				$commanders[$row['id']] = $c;
				$persisted[] = $row['id'];
				continue;
			}
			$this->format($row, $commanders, $currentId, $unPersisted);
		}
		return array_values($commanders);
	}
	
	/**
	 * @param array $ids
	 * @return array
	 */
	public function getCommandersByIds($ids = [])
	{
		$statement = $this->select('WHERE c.id IN (' . implode(',', $ids) . ')');
		$commanders = [];
		$currentId = 0;
		$persisted = [];
		$unPersisted = [];
		while ($row = $statement->fetch()) {
			if (in_array($row['id'], $persisted)) {
				continue;
			}
			if (!in_array($row['id'], $unPersisted) && ($c = $this->unitOfWork->getObject(Commander::class, $row['id'])) !== null) {
				$currentId = $row['id'];
				$commanders[$row['id']] = $c;
				$persisted[] = $row['id'];
				continue;
			}
			$this->format($row, $commanders, $currentId, $unPersisted);
		}
		return array_values($commanders);
	}
	
	/**
	 * @return array
	 */
	public function getMovingCommanders()
	{
		$statement = $this->select('WHERE c.statement = ' . Commander::MOVING);
		$commanders = [];
		$currentId = 0;
		$persisted = [];
		$unPersisted = [];
		while ($row = $statement->fetch()) {
			if (in_array($row['id'], $persisted)) {
				continue;
			}
			if (!in_array($row['id'], $unPersisted) && ($c = $this->unitOfWork->getObject(Commander::class, $row['id'])) !== null) {
				$currentId = $row['id'];
				$commanders[$row['id']] = $c;
				$persisted[] = $row['id'];
				continue;
			}
			$this->format($row, $commanders, $currentId, $unPersisted);
		}
		return array_values($commanders);
	}
	
	/**
	 * @param int $playerId
	 * @param array $statements
	 * @param array $orderBy
	 * @return array
	 */
	public function getPlayerCommanders($playerId, $statements = [], $orderBy = [])
	{
		$statementClause = (!empty($statements)) ? ' AND c.statement IN (' . implode(',', $statements) . ') ' : '';
		$statement = $this->select('WHERE c.rPlayer = :player_id ' . $statementClause . $this->getOrderByClause($orderBy), ['player_id' => $playerId]);
		$commanders = [];
		$currentId = 0;
		$persisted = [];
		$unPersisted = [];
		while ($row = $statement->fetch()) {
			if (in_array($row['id'], $persisted)) {
				continue;
			}
			if (!in_array($row['id'], $unPersisted) && ($c = $this->unitOfWork->getObject(Commander::class, $row['id'])) !== null) {
				$currentId = $row['id'];
				$commanders[$row['id']] = $c;
				$persisted[] = $row['id'];
				continue;
			}
			$this->format($row, $commanders, $currentId, $unPersisted);
		}
		return array_values($commanders);
	}
	
	/**
	 * @param int $orbitalBaseId
	 * @param int $line
	 * @return array
	 */
	public function getCommandersByLine($orbitalBaseId, $line)
	{
		$statement = $this->select('WHERE c.rBase = :base_id AND c.line = :line', ['base_id' => $orbitalBaseId, 'line' => $line]);
		$commanders = [];
		$currentId = 0;
		$persisted = [];
		$unPersisted = [];
		while ($row = $statement->fetch()) {
			if (in_array($row['id'], $persisted)) {
				continue;
			}
			if (!in_array($row['id'], $unPersisted) && ($c = $this->unitOfWork->getObject(Commander::class, $row['id'])) !== null) {
				$currentId = $row['id'];
				$commanders[$row['id']] = $c;
				$persisted[] = $row['id'];
				continue;
			}
			$this->format($row, $commanders, $currentId, $unPersisted);
		}
		return array_values($commanders);
	}
	
	/**
	 * @param int $playerId
	 * @return array
	 */
	public function getIncomingAttacks($playerId)
	{
		$statement = $this->select(
			'WHERE dp.rPlayer = :player_id '.
			'AND c.statement = ' . Commander::MOVING . ' ' .
			'AND c.travelType IN (' . Commander::COLO . ', ' . Commander::LOOT. ')'
		, ['player_id' => $playerId]);
		$commanders = [];
		$currentId = 0;
		$persisted = [];
		$unPersisted = [];
		while ($row = $statement->fetch()) {
			if (in_array($row['id'], $persisted)) {
				continue;
			}
			if (!in_array($row['id'], $unPersisted) && ($c = $this->unitOfWork->getObject(Commander::class, $row['id'])) !== null) {
				$currentId = $row['id'];
				$commanders[$row['id']] = $c;
				$persisted[] = $row['id'];
				continue;
			}
			$this->format($row, $commanders, $currentId, $unPersisted);
		}
		return array_values($commanders);
	}
	
	/**
	 * @param int $playerId
	 * @return array
	 */
	public function getOutcomingAttacks($playerId)
	{
		$statement = $this->select(
			'WHERE o.rPlayer = :player_id '.
			'AND c.statement = ' . Commander::MOVING
		, ['player_id' => $playerId]);
		$commanders = [];
		$currentId = 0;
		$persisted = [];
		$unPersisted = [];
		while ($row = $statement->fetch()) {
			if (in_array($row['id'], $persisted)) {
				continue;
			}
			if (!in_array($row['id'], $unPersisted) && ($c = $this->unitOfWork->getObject(Commander::class, $row['id'])) !== null) {
				$currentId = $row['id'];
				$commanders[$row['id']] = $c;
				$persisted[] = $row['id'];
				continue;
			}
			$this->format($row, $commanders, $currentId, $unPersisted);
		}
		return array_values($commanders);
	}
	
	/**
	 * @param array $placeId
	 * @return array
	 */
	public function getIncomingCommanders($placeId)
	{
		$statement = $this->select(
			'WHERE c.rDestinationPlace = :place_id AND c.statement = ' . Commander::MOVING . ' ORDER BY c.dArrival ASC'
		, ['place_id' => $placeId]);
		$commanders = [];
		$currentId = 0;
		$persisted = [];
		$unPersisted = [];
		while ($row = $statement->fetch()) {
			if (in_array($row['id'], $persisted)) {
				continue;
			}
			if (!in_array($row['id'], $unPersisted) && ($c = $this->unitOfWork->getObject(Commander::class, $row['id'])) !== null) {
				$currentId = $row['id'];
				$commanders[$row['id']] = $c;
				$persisted[] = $row['id'];
				continue;
			}
			$this->format($row, $commanders, $currentId, $unPersisted);
		}
		return array_values($commanders);
	}
	
	/**
	 * @param int $orbitalBaseId
	 * @param int $line
	 * @return int
	 */
	public function countCommandersByLine($orbitalBaseId, $line)
	{
		$statement = $this->connection->prepare('SELECT COUNT(*) AS nb_commanders FROM commander WHERE statement IN (' . Commander::AFFECTED . ', ' . Commander::MOVING . ') AND rBase = :orbital_base_id AND line = :line');
		$statement->execute(['orbital_base_id' => $orbitalBaseId, 'line' => $line]);
		return (int) $statement->fetch()['nb_commanders'];
	}
	
	public function insert($commander)
	{
		$statement = $this->connection->prepare(
			'INSERT INTO commander SET 
			name = :name,
			avatar = :avatar,
			rPlayer = :player_id,
			rBase = :base_id,
			sexe = :gender,
			age = :age,
			level = :level,
			experience = :experience,
			uCommander = :u_commander,
			statement = :statement,
			dCreation = :created_at'
		);
		$statement->execute([
			'name' => $commander->name,
			'avatar' => $commander->avatar,
			'player_id' => $commander->rPlayer,
			'base_id' => $commander->rBase,
			'gender' => $commander->sexe,
			'age' => $commander->age,
			'level' => $commander->level,
			'experience' => $commander->experience,
			'u_commander' => Utils::now(),
			'statement' => $commander->statement,
			'created_at' => $commander->dCreation,
		]);
		$commander->setId($this->connection->lastInsertId());
		$nbrSquadrons = $commander->getLevel();
		
		$squadronStatement = $this->connection->prepare('INSERT INTO squadron(rCommander, dCreation) VALUES(:commander_id, NOW())');

		for ($i = 0; $i < $nbrSquadrons; $i++) {
			$squadronStatement->execute(['commander_id' => $commander->getId()]);
		}

		$lastSquadronId = $this->connection->lastInsertId();
		$armySize = count($commander->getArmy());
		for ($i = 0; $i < $armySize; $i++) {
			$commander->getSquadron[$i]->setId($lastSquadronId);
			$lastSquadronId--;
		}
	}
	
	public function update($commander)
	{
		$statement = $this->connection->prepare(
			'UPDATE commander SET				
				name = :name,
				avatar = :avatar,
				rPlayer = :player_id,
				rBase = :base_id,
				comment = :comment,
				sexe = :gender,
				age = :age,
				level = :level,
				experience = :experience,
				uCommander = :u_commander,
				palmares = :palmares,
				statement = :statement,
				`line` = :line,
				dStart = :started_at,
				dArrival = :arrived_at,
				resources = :resources,
				travelType = :travel_type,
				travelLength = :travel_length,
				rStartPlace	= :start_place_id,
				rDestinationPlace = :destination_place_id,
				dCreation = :created_at,
				dAffectation = :affected_at,
				dDeath = :died_at
			WHERE id = :id'
		);
		$statement->execute([				
			'name' => $commander->name,
			'avatar' => $commander->avatar,
			'player_id' => $commander->rPlayer,
			'base_id' => $commander->rBase,
			'comment' => $commander->comment,
			'gender' => $commander->sexe,
			'age' => $commander->age,
			'level' => $commander->level,
			'experience' => $commander->experience,
			'u_commander' => $commander->uCommander,
			'palmares' => $commander->palmares,
			'statement' => $commander->statement,
			'line' => $commander->line,
			'started_at' => $commander->dStart,
			'arrived_at' => $commander->dArrival,
			'resources' => $commander->resources,
			'travel_type' => $commander->travelType,
			'travel_length' => $commander->travelLength,
			'start_place_id' => $commander->rStartPlace,
			'destination_place_id' => $commander->rDestinationPlace,
			'created_at' => $commander->dCreation,
			'affected_at' => $commander->dAffectation,
			'died_at' => $commander->dDeath,
			'id' => $commander->id
		]);
		$squadronStatement = $this->connection->prepare(
			'UPDATE squadron SET
				rCommander = :commander_id,
				ship0 = :ship_0,
				ship1 = :ship_1,
				ship2 = :ship_2,
				ship3 = :ship_3,
				ship4 = :ship_4,
				ship5 = :ship_5,
				ship6 = :ship_6,
				ship7 = :ship_7,
				ship8 = :ship_8,
				ship9 = :ship_9,
				ship10 = :ship_10,
				ship11 = :ship_11,
				DLAstModification = NOW()
			WHERE id = :id'
		);
		foreach($commander->getArmy() as $squadron) {
			if ($squadron->getId() === 0) {
				continue;
			}
			$squadronStatement->execute([
				'commander_id' => $squadron->getRCommander(),
				'ship_0' => $squadron->getNbrShipByType(0),
				'ship_1' => $squadron->getNbrShipByType(1),
				'ship_2' => $squadron->getNbrShipByType(2),
				'ship_3' => $squadron->getNbrShipByType(3),
				'ship_4' => $squadron->getNbrShipByType(4),
				'ship_5' => $squadron->getNbrShipByType(5),
				'ship_6' => $squadron->getNbrShipByType(6),
				'ship_7' => $squadron->getNbrShipByType(7),
				'ship_8' => $squadron->getNbrShipByType(8),
				'ship_9' => $squadron->getNbrShipByType(9),
				'ship_10' => $squadron->getNbrShipByType(10),
				'ship_11' => $squadron->getNbrShipByType(11),
				'id' => $squadron->getId()
			]);
		}
		if ($commander->getLevel() > $commander->getSizeArmy()) {
			//on créé un nouveau squadron avec rCommander correspondant
			$nbrSquadronToCreate = $commander->getLevel() - $commander->getSizeArmy();
			$qr = $this->connection->prepare('INSERT INTO squadron (rCommander, dCreation) VALUES (?, NOW())');
			for ($i = 0; $i < $nbrSquadronToCreate; $i++) {
				$qr->execute([$commander->getId()]);
				$commander->squadronsIds[] = $this->connection->lastInsertId();
			}
		}
	}
	
	/**
	 * @param Commander $commander
	 */
	public function updateExperience(Commander $commander, $earnedExperience, $earnedLevel)
	{
		$statement = $this->connection->prepare(
			'UPDATE commander SET
				experience = experience + :experience,
				level = level + :level,
				uCommander = :updated_at
			WHERE id = :id'
		);
		$statement->execute([
			'experience' => $earnedExperience,
			'level' => $earnedLevel,
			'updated_at' => $commander->getUpdatedAt(),
			'id' => $commander->getId()
		]);
	}
	
	public function remove($commander)
	{
		$statement = $this->connection->prepare('DELETE FROM commander WHERE id = :id');
		$statement->execute(['id' => $commander->getId()]);
	}
	
	public function format($data, &$commanders, &$currentId = null, &$unPersisted = [])
	{
		if ($currentId === null || (int) $data['id'] !== $currentId) {
			$currentId = $data['id'];
			$commanders[$currentId] = new Commander();

			$commanders[$currentId]->id = (int) $data['id'];
			$commanders[$currentId]->name = $data['name'];
			$commanders[$currentId]->avatar = $data['avatar'];
			$commanders[$currentId]->rPlayer = (int) $data['rPlayer'];
			$commanders[$currentId]->playerName = $data['pName'];
			$commanders[$currentId]->playerColor = $data['pColor'];
			$commanders[$currentId]->rBase = (int) $data['rBase'];
			$commanders[$currentId]->comment = $data['comment'];
			$commanders[$currentId]->sexe = $data['sexe'];
			$commanders[$currentId]->age = (int) $data['age'];
			$commanders[$currentId]->level = (int) $data['level'];
			$commanders[$currentId]->experience = (int) $data['experience'];
			$commanders[$currentId]->uCommander = $data['uCommander'];
			$commanders[$currentId]->palmares = $data['palmares'];
			$commanders[$currentId]->statement = (int) $data['statement'];
			$commanders[$currentId]->line = (int) $data['line'];
			$commanders[$currentId]->dCreation = $data['dCreation'];
			$commanders[$currentId]->dAffectation = $data['dAffectation'];
			$commanders[$currentId]->dDeath = $data['dDeath'];
			$commanders[$currentId]->oBName = $data['oName'];

			$commanders[$currentId]->dStart = $data['dStart'];
			$commanders[$currentId]->dArrival = $data['dArrival'];
			$commanders[$currentId]->resources = (int) $data['resources'];
			$commanders[$currentId]->travelType = $data['travelType'];
			$commanders[$currentId]->travelLength = $data['travelLength'];
			$commanders[$currentId]->rStartPlace = $data['rStartPlace'];
			$commanders[$currentId]->rDestinationPlace = $data['rDestinationPlace'];

			$commanders[$currentId]->startPlaceName = ($data['spName'] == '') ? 'planète rebelle' : $data['spName'];
			$commanders[$currentId]->destinationPlaceName = ($data['dpName'] == '') ? 'planète rebelle' : $data['dpName'];
			$commanders[$currentId]->destinationPlacePop = $data['destinationPlacePop'];
			$commanders[$currentId]->startPlacePop = $data['startPlacePop'];
			
			$this->unitOfWork->addObject($commanders[$currentId]);
			$unPersisted[] = $currentId;
		}
		$commanders[$currentId]->squadronsIds[] = (int) $data['sqId'];
		$commanders[$currentId]->armyInBegin[] = [
			$data['sqShip0'], 
			$data['sqShip1'], 
			$data['sqShip2'], 
			$data['sqShip3'], 
			$data['sqShip4'], 
			$data['sqShip5'], 
			$data['sqShip6'], 
			$data['sqShip7'],
			$data['sqShip8'], 
			$data['sqShip9'], 
			$data['sqShip10'], 
			$data['sqShip11'], 
			$data['sqDCreation'], 
			$data['sqDLastModification']
		];
	}
}
