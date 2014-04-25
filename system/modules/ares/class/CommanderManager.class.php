<?php

/**
 * Commander Manager
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Arès
 * @update 20.05.13
*/
// !! lors d'un load, mettre c. avant les attribut where

class CommanderManager extends Manager {
	protected $managerType = '_Commander';

	//charge depuis la base de donnée avec ce qu'on veut
	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where);
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT c.*,
				o.iSchool, o.name AS oName,
				p.name AS pName,
				p.rColor AS pColor,
				dp.name AS dpName,
				sp.name AS spName
			FROM commander AS c
			LEFT JOIN orbitalBase AS o
				ON o.rPlace = c.rBase
			LEFT JOIN player AS p
				ON p.id = c.rPlayer
			LEFT JOIN orbitalBase AS dp
				ON dp.rPlace = c.rDestinationPlace
			LEFT JOIN orbitalBase AS sp
				ON sp.rPlace = c.rStartPlace

			' . $formatWhere .'
			' . $formatOrder .'
			' . $formatLimit
		);

		foreach($where AS $v) {
			if (is_array($v)) {
				foreach ($v as $p) {
					$valuesArray[] = $p;
				}
			} else {
				$valuesArray[] = $v;
			}
		}
		
		if (empty($valuesArray)) {
			$qr->execute();
		} else {
			$qr->execute($valuesArray);
		}

		$awCommanders = $qr->fetchAll();
		$qr->closeCursor();

		if (count($awCommanders) > 0) {
			$idCommandersArray = array();
			foreach ($awCommanders AS $commander) {
				$idCommandersArray[] = $commander['id'];
			}

			$qr = 'SELECT * FROM squadron ';
			$i = 0;
			foreach ($idCommandersArray AS $id) {
				$qr .= ($i == 0) ? 'WHERE rCommander = ? ' : 'OR rCommander = ? ';
				$i++;
			}

			$qr = $db->prepare($qr);

			if (empty($idCommandersArray)) {
				$qr->execute();
			} else {
				$qr->execute($idCommandersArray);
			}

			$awSquadrons = $qr->fetchAll();
			$arrayOfArmies = array();
			$squadronsIds = array();

			foreach ($awSquadrons AS $squadron) {
				$id =  $squadron[0];
				$rCommander = $squadron[1];
				unset($squadron[0], $squadron[1]);
				$squadron = array_merge($squadron);
				$arrayOfArmies[''.$rCommander.''][] = $squadron;
				$squadronsIds[''.$rCommander.''][] = $id;
			}

			foreach ($awCommanders AS $awCommander) {
				$commander = new Commander();

				$commander->id = $awCommander['id'];
				$commander->name = $awCommander['name'];
				$commander->avatar = $awCommander['avatar'];
				$commander->rPlayer = $awCommander['rPlayer'];
				$commander->playerName = $awCommander['pName'];
				$commander->playerColor = $awCommander['pColor'];
				$commander->rBase = $awCommander['rBase'];
				$commander->comment = $awCommander['comment'];
				$commander->sexe = $awCommander['sexe'];
				$commander->age = $awCommander['age'];
				$commander->level = $awCommander['level'];
				$commander->experience = $awCommander['experience'];
				$commander->uCommander = $awCommander['uCommander'];
				$commander->palmares = $awCommander['palmares'];
				$commander->statement = $awCommander['statement'];
				$commander->line = $awCommander['line'];
				$commander->dCreation = $awCommander['dCreation'];
				$commander->dAffectation = $awCommander['dAffectation'];
				$commander->dDeath = $awCommander['dDeath'];
				$commander->oBName = $awCommander['oName'];

				$commander->dStart = $awCommander['dStart'];
				$commander->dArrival = $awCommander['dArrival'];
				$commander->resources = $awCommander['resources'];
				$commander->travelType = $awCommander['travelType'];
				$commander->travelLength = $awCommander['travelLength'];
				$commander->rStartPlace = $awCommander['rStartPlace'];
				$commander->rDestinationPlace = $awCommander['rDestinationPlace'];

				$commander->startPlaceName = $awCommander['spName'];
				$commander->destinationPlaceName	= $awCommander['dpName'];

				$commander->setSquadronsIds($squadronsIds[$commander->getId()]);

				$commander->setArmyInBegin($arrayOfArmies[$commander->getId()]);
				$commander->setArmy();
				$commander->setPevInBegin();

				$currentCommander = $this->_Add($commander);

				if ($this->currentSession->getUMode()) {
					$currentCommander->uCommander();
				}
			}
		}
	}

	//inscrit un nouveau commandant en bdd
	public function add($newCommander) {
		$db = DataBase::getInstance();
		$qr = 'INSERT INTO commander
		SET 
			name = ?,
			avatar = ?,
			rPlayer = ?,
			rBase = ?,
			sexe = ?,
			age = ?,
			level = ?,
			experience = ?,
			uCommander = ?,
			statement = ?,
			dCreation = ?';
		$qr = $db->prepare($qr);
		$aw = $qr->execute(array(
			$newCommander->name,
			$newCommander->avatar,
			$newCommander->rPlayer,
			$newCommander->rBase,
			$newCommander->sexe,
			$newCommander->age,
			$newCommander->level,
			$newCommander->experience,
			Utils::now(),
			$newCommander->statement,
			$newCommander->dCreation,
			));
		$newCommander->setId($db->lastInsertId());

		$nbrSquadrons = $newCommander->getLevel();
		$maxId = $db->lastInsertId();
		$qr2 = 'INSERT INTO 
			squadron(rCommander, dCreation)
			VALUES(?, NOW())';
		$qr2 = $db->prepare($qr2);

		for ($i = 0; $i < $nbrSquadrons; $i++) {
			$aw2 = $qr2->execute(array($maxId));
		}

		$lastSquadronId = $db->lastInsertId();
		for ($i = 0; $i < count($newCommander->getArmy()); $i++) {
			$newCommander->getSquadron[$i]->setId($lastSquadronId);
			$lastSquadronId--;
		}

		$this->_Add($newCommander);
	}

	//réécrit la base de donnée (à l'issue d'un combat par exemple)
	public function save() {
		$commanders = $this->_Save();
		
		foreach ($commanders AS $k => $commander) {
			$db = DataBase::getInstance();
			$qr = 'UPDATE commander
				SET				
					name = ?,
					avatar = ?,
					rPlayer = ?,
					rBase = ?,
					comment = ?,
					sexe = ?,
					age = ?,
					level = ?,
					experience = ?,
					uCommander = ?,
					palmares = ?,
					statement = ?,
					`line` = ?,
					dStart = ?,
					dArrival = ?,
					resources = ?,
					travelType = ?,
					travelLength = ?,
					rStartPlace	= ?,
					rDestinationPlace = ?,
					dCreation = ?,
					dAffectation = ?,
					dDeath = ?

				WHERE id = ?';

			$qr = $db->prepare($qr);
			//uper les commandants
			$qr->execute(array( 				
				$commander->name,
				$commander->avatar,
				$commander->rPlayer,
				$commander->rBase,
				$commander->comment,
				$commander->sexe,
				$commander->age,
				$commander->level,
				$commander->experience,
				$commander->uCommander,
				$commander->palmares,
				$commander->statement,
				$commander->line,
				$commander->dStart,
				$commander->dArrival,
				$commander->resources,
				$commander->travelType,
				$commander->travelLength,
				$commander->rStartPlace,
				$commander->rDestinationPlace,
				$commander->dCreation,
				$commander->dAffectation,
				$commander->dDeath,
				$commander->id));

			$qr = 'UPDATE squadron SET
				rCommander = ?,
				ship0 = ?,
				ship1 = ?,
				ship2 = ?,
				ship3 = ?,
				ship4 = ?,
				ship5 = ?,
				ship6 = ?,
				ship7 = ?,
				ship8 = ?,
				ship9 = ?,
				ship10 = ?,
				ship11 = ?,
				DLAstModification = NOW()
			WHERE id = ?';

			$qr = $db->prepare($qr);
			$army = $commander->getArmy();

			foreach ($army AS $squadron) {
				//uper les escadrilles
				$qr->execute(array(
					$squadron->getRCommander(),
					$squadron->getNbrShipByType(0),
					$squadron->getNbrShipByType(1),
					$squadron->getNbrShipByType(2),
					$squadron->getNbrShipByType(3),
					$squadron->getNbrShipByType(4),
					$squadron->getNbrShipByType(5),
					$squadron->getNbrShipByType(6),
					$squadron->getNbrShipByType(7),
					$squadron->getNbrShipByType(8),
					$squadron->getNbrShipByType(9),
					$squadron->getNbrShipByType(10),
					$squadron->getNbrShipByType(11),
					$squadron->getId()
				));
			}
			if ($commander->getLevel() > $commander->getSizeArmy()) {
				//on créé une nouvelle squadron avec rCommander correspondant
				$nbrSquadronToCreate = $commander->getLevel() - $commander->getSizeArmy();
				$qr = 'INSERT INTO 
				squadron (rCommander, dCreation)	
				VALUES (' . $commander->getId() . ', NOW())';
				$i = 1;
				while ($i < $nbrSquadronToCreate) {
					$qr .= ',(' . $commander->getId() . ', NOW())';
					$i++;
				}
				$qr = $db->prepare($qr);
				$qr->execute();
			}
		}
		$this->isUpdate = TRUE;
	}

	public function setCommander($commander) {
		$this->objects['' . $commander->getId() .''] = $commander;
	}
}
