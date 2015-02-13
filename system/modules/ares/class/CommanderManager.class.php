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
			LEFT JOIN orbitalBase AS o
				ON o.rPlace = c.rBase
			LEFT JOIN player AS p
				ON p.id = c.rPlayer
			LEFT JOIN orbitalBase AS dp
				ON dp.rPlace = c.rDestinationPlace
			LEFT JOIN orbitalBase AS sp
				ON sp.rPlace = c.rStartPlace
			LEFT JOIN squadron AS sq
				ON sq.rCommander = c.id
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
			for ($i = 0; $i < count($awCommanders); $i++) {
				if ($i == 0 || $awCommanders[$i]['id'] != $awCommanders[$i - 1]['id']) {
					$commander = new Commander();

					$commander->id = $awCommanders[$i]['id'];
					$commander->name = $awCommanders[$i]['name'];
					$commander->avatar = $awCommanders[$i]['avatar'];
					$commander->rPlayer = $awCommanders[$i]['rPlayer'];
					$commander->playerName = $awCommanders[$i]['pName'];
					$commander->playerColor = $awCommanders[$i]['pColor'];
					$commander->rBase = $awCommanders[$i]['rBase'];
					$commander->comment = $awCommanders[$i]['comment'];
					$commander->sexe = $awCommanders[$i]['sexe'];
					$commander->age = $awCommanders[$i]['age'];
					$commander->level = $awCommanders[$i]['level'];
					$commander->experience = $awCommanders[$i]['experience'];
					$commander->uCommander = $awCommanders[$i]['uCommander'];
					$commander->palmares = $awCommanders[$i]['palmares'];
					$commander->statement = $awCommanders[$i]['statement'];
					$commander->line = $awCommanders[$i]['line'];
					$commander->dCreation = $awCommanders[$i]['dCreation'];
					$commander->dAffectation = $awCommanders[$i]['dAffectation'];
					$commander->dDeath = $awCommanders[$i]['dDeath'];
					$commander->oBName = $awCommanders[$i]['oName'];

					$commander->dStart = $awCommanders[$i]['dStart'];
					$commander->dArrival = $awCommanders[$i]['dArrival'];
					$commander->resources = $awCommanders[$i]['resources'];
					$commander->travelType = $awCommanders[$i]['travelType'];
					$commander->travelLength = $awCommanders[$i]['travelLength'];
					$commander->rStartPlace = $awCommanders[$i]['rStartPlace'];
					$commander->rDestinationPlace = $awCommanders[$i]['rDestinationPlace'];

					$commander->startPlaceName = ($awCommanders[$i]['spName'] == '') ? 'planète rebelle' : $awCommanders[$i]['spName'];
					$commander->destinationPlaceName = ($awCommanders[$i]['dpName'] == '') ? 'planète rebelle' : $awCommanders[$i]['dpName'];
				}

				$commander->squadronsIds[] = $awCommanders[$i]['sqId'];

				$commander->armyInBegin[] = array(
					$awCommanders[$i]['sqShip0'], 
					$awCommanders[$i]['sqShip1'], 
					$awCommanders[$i]['sqShip2'], 
					$awCommanders[$i]['sqShip3'], 
					$awCommanders[$i]['sqShip4'], 
					$awCommanders[$i]['sqShip5'], 
					$awCommanders[$i]['sqShip6'], 
					$awCommanders[$i]['sqShip7'], 
					$awCommanders[$i]['sqShip8'], 
					$awCommanders[$i]['sqShip9'], 
					$awCommanders[$i]['sqShip10'], 
					$awCommanders[$i]['sqShip11'], 
					$awCommanders[$i]['sqDCreation'], 
					$awCommanders[$i]['sqDLastModification']);
					
				if ($i == count($awCommanders) - 1 || $awCommanders[$i]['id'] != $awCommanders[$i + 1]['id']) {
					$currentCommander = $this->_Add($commander);

					if ($this->currentSession->getUMode()) {
						$currentCommander->uCommander();
					}
				}
			}
		}
	}

	public function emptySession() {
		# empty the session, for player rankings
		$this->_EmptyCurrentSession();
		$this->newSession(FALSE);
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
