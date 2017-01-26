<?php

namespace Asylamba\Modules\Ares\Repository;

use Asylamba\Classes\Entity\AbstractRepository;

use Asylamba\Modules\Ares\Model\Commander;

class CommanderRepository extends AbstractRepository {
	public function select($clause, $params)
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
	}
	
	public function update($commander)
	{
		$qr = $this->connection->prepare(
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
		$qr->execute([				
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
	}
	
	public function remove($commander)
	{
		
	}
	
	public function format($data, &$currentCommander = null)
	{
		if ($i == 0 || (int) $data['id'] !== $currentCommander->id) {
			$currentCommander = new Commander();

			$currentCommander->id = (int) $data['id'];
			$currentCommander->name = $data['name'];
			$currentCommander->avatar = $data['avatar'];
			$currentCommander->rPlayer = (int) $data['rPlayer'];
			$currentCommander->playerName = $data['pName'];
			$currentCommander->playerColor = $data['pColor'];
			$currentCommander->rBase = (int) $data['rBase'];
			$currentCommander->comment = $data['comment'];
			$currentCommander->sexe = $data['sexe'];
			$currentCommander->age = (int) $data['age'];
			$currentCommander->level = (int) $data['level'];
			$currentCommander->experience = (int) $data['experience'];
			$currentCommander->uCommander = $data['uCommander'];
			$currentCommander->palmares = $data['palmares'];
			$currentCommander->statement = $data['statement'];
			$currentCommander->line = (int) $data['line'];
			$currentCommander->dCreation = $data['dCreation'];
			$currentCommander->dAffectation = $data['dAffectation'];
			$currentCommander->dDeath = $data['dDeath'];
			$currentCommander->oBName = $data['oName'];

			$currentCommander->dStart = $data['dStart'];
			$currentCommander->dArrival = $data['dArrival'];
			$currentCommander->resources = (int) $data['resources'];
			$currentCommander->travelType = $data['travelType'];
			$currentCommander->travelLength = $data['travelLength'];
			$currentCommander->rStartPlace = $data['rStartPlace'];
			$currentCommander->rDestinationPlace = $data['rDestinationPlace'];

			$currentCommander->startPlaceName = ($data['spName'] == '') ? 'planète rebelle' : $data['spName'];
			$currentCommander->destinationPlaceName = ($data['dpName'] == '') ? 'planète rebelle' : $data['dpName'];
			$currentCommander->destinationPlacePop = $data['destinationPlacePop'];
			$currentCommander->startPlacePop = $data['startPlacePop'];
		}
		$currentCommander->squadronsIds[] = (int) $data['sqId'];
		$currentCommander->armyInBegin[] = [
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