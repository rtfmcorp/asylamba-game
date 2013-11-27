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

class CommanderManager extends Manager {
	protected $managerType = '_Commander';

	//charge depuis la base de donnée avec ce qu'on veut
	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'c.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT c.*,
				o.iSchool, o.name AS oName,
				p.name AS pName,
				p.rColor AS pColor,
				do.name AS doName
			FROM commander AS c
			LEFT JOIN orbitalBase AS o
				ON o.rPlace = c.rBase
			LEFT JOIN player AS p
				ON p.id = c.rPlayer
			LEFT JOIN orbitalBase AS do
				ON do.rPlace = c.rPlaceDestination

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

				$commander->setId($awCommander['id']);
				$commander->setName($awCommander['name']);
				$commander->setAvatar($awCommander['avatar']);
				$commander->setRPlayer($awCommander['rPlayer']);
				$commander->setPlayerName($awCommander['pName']);
				$commander->setPlayerColor($awCommander['pColor']);
				$commander->setRBase($awCommander['rBase']);
				$commander->setComment($awCommander['comment']);
				$commander->setSexe($awCommander['sexe']);
				$commander->setAge($awCommander['age']);
				$commander->setLevel($awCommander['level']);
				$commander->setExperience($awCommander['experience']);
				$commander->setUExperience($awCommander['uExperience']);
				$commander->setPalmares($awCommander['palmares']);
				$commander->setTypeOfMove($awCommander['typeOfMove']);
				$commander->setrPlaceDestination($awCommander['rPlaceDestination']);
				$commander->setArrivalDate($awCommander['arrivalDate']);
				$commander->setResourcesTransported($awCommander['resourcesTransported']);
				$commander->setStatement($awCommander['statement']);
				$commander->setDCreation($awCommander['dCreation']);
				$commander->setDAffectation($awCommander['dAffectation']);
				$commander->setDDeath($awCommander['dDeath']);
				$commander->setOBName($awCommander['oName']);
				$commander->setDestinationPlaceName($awCommander['doName']);

				$commander->setSquadronsIds($squadronsIds[$commander->getId()]);

				$currentCommander = $this->_Add($commander);

				$commander->setArmyInBegin($arrayOfArmies[$commander->getId()]);
				$commander->setArmy();
				
				if ($this->currentSession->getUMode() == TRUE) {
					$currentCommander->uTravel();

					if ($currentCommander->getStatement() == 0) {
						$currentCommander->uExperienceInSchool($awCommander['iSchool']);
					}
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
			uExperience = ?,
			statement = ?,
			dCreation = ?';
		$qr = $db->prepare($qr);
		$aw = $qr->execute(array(
			$newCommander->getName(),
			$newCommander->getAvatar(),
			$newCommander->getRPlayer(),
			$newCommander->getRBase(),
			$newCommander->getSexe(),
			$newCommander->getAge(),
			$newCommander->getLevel(),
			$newCommander->getExperience(),
			$newCommander->getUExperience(),
			$newCommander->getStatement(),
			$newCommander->getDCreation(),
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
					uExperience = ?,
					palmares = ?,
					typeOfMove = ?,
					rPlaceDestination = ?,
					arrivalDate = ?,
					resourcesTransported = ?,
					statement = ?,
					dCreation = ?,
					dAffectation = ?,
					dDeath = ? 
				WHERE id = ?';

			$qr = $db->prepare($qr);
			//uper les commandants
			$qr->execute(array( 				
				$commander->getName(),
				$commander->getAvatar(),
				$commander->getRPlayer(),
				$commander->getRBase(),
				$commander->getComment(),
				$commander->getSexe(),
				$commander->getAge(),
				$commander->getLevel(),
				$commander->getExperience(),
				$commander->getUExperience(),
				$commander->getPalmares(),
				$commander->getTypeOfMove(),
				$commander->getrPlaceDestination(),
				$commander->getArrivalDate(),
				$commander->getResourcesTransported(),
				$commander->getStatement(),
				$commander->getDCreation(),
				$commander->getDAffectation(),
				$commander->getDDeath(),
				$commander->getId()));

			$qr = 'UPDATE squadron SET
				rCommander = ?,
				pegase = ?,
				satyre = ?,
				chimere = ?,
				sirene = ?,
				dryade = ?,
				meduse = ?,
				griffon = ?,
				cyclope = ?,
				minotaure = ?,
				hydre = ?,
				cerbere = ?,
				phenix = ?,
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
