<?php

/**
 * Commander In Fight Manager
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Arès
 * @update 20.05.13
*/

class CommanderInFightManager extends Manager {
	protected $managerType = '_CommanderInFight';

	//charge depuis la base de donnée avec ce qu'on veut
	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'c.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT c.*,
		o.iSchool, o.name AS oName,
		p.Name AS pName
		FROM commander AS c
		LEFT JOIN orbitalBase AS o
			ON o.rPlace = c.rBase
		LEFT JOIN player AS p
			ON p.id = c.rPlayer
		' . $formatWhere .'
		' . $formatOrder .'
		' . $formatLimit);

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

		$idCommandersArray = array();
		foreach ($awCommanders AS $commander) {
			$idCommandersArray[] = $commander['id'];
		}

		$qr = 'SELECT id, rCommander, pegase, satyre, chimere, sirene, dryade, meduse, griffon, cyclope, minotaure, hydre, cerbere, phenix, DLAstModification 
			FROM squadron ';

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
			$arrayOfArmies[$rCommander][] = $squadron;
			$squadronsIds[$rCommander][] = $id;
		}

		foreach ($awCommanders AS $awCommander) {
			$commander = new CommanderInFight();

			$commander->setId($awCommander['id']); 	
			$commander->setRPlayer($awCommander['rPlayer']);
			$commander->setPlayerName($awCommander['pName']);								
			$commander->setName($awCommander['name']); 								
			$commander->setAvatar($awCommander['avatar']); 							
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

			$commander->setArmyInBegin($arrayOfArmies[$commander->getId()]);
			$commander->setSquadronsIds($squadronsIds[$commander->getId()]);						
			$commander->setArmy($arrayOfArmies[$commander->getId()]);

			$currentCommander = $this->_Add($commander);
			// if ($this->currentSession->getUMode() == TRUE) {
			// 	$currentCommander->uTravel();
			// }
		}
	}
}
