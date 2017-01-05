<?php

/**
 * Commercial Route Manager
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Athena
 * @update 20.05.13
*/
namespace Asylamba\Modules\Athena\Manager;

use Asylamba\Classes\Worker\Manager;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Database\Database;
use Asylamba\Modules\Athena\Model\CommercialRoute;
use Asylamba\Modules\Demeter\Model\Color;

class CommercialRouteManager extends Manager {
	protected $managerType = '_CommercialRoute';

	/**
	 * @param Database $database
	 */
	public function __construct(Database $database) {
		parent::__construct($database);
	}
	
	//METHODS
	public function load($where, $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'cr.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$qr = $this->database->prepare('SELECT 
			cr.id AS id,
			cr.rOrbitalBase AS rOrbitalBase,
			cr.rOrbitalBaseLinked AS rOrbitalBaseLinked,
			cr.imageLink AS imageLink,
			cr.distance AS distance,
			cr.price AS price,
			cr.income AS income,
			cr.dProposition AS dProposition,
			cr.dCreation AS dCreation,
			cr.statement AS statement,

			ob1.rPlayer AS playerId1,
			ob1.name AS baseName1,
			ob1.typeOfBase AS baseType1,
			pl1.name AS playerName1,
			pl1.rColor AS playerColor1,
			pl1.avatar AS avatar1,
			p1.population AS population1,

			ob2.rPlayer AS playerId2,
			ob2.name AS baseName2,
			ob2.typeOfBase AS baseType2,
			pl2.name AS playerName2,
			pl2.rColor AS playerColor2,
			pl2.avatar AS avatar2,
			p2.population AS population2

			FROM commercialRoute AS cr

			LEFT JOIN orbitalBase AS ob1
			ON cr.rOrbitalBase = ob1.rPlace
			LEFT JOIN player AS pl1
			ON ob1.rPlayer = pl1.id
			LEFT JOIN place AS p1
			ON ob1.rPlace = p1.id

			LEFT JOIN orbitalBase AS ob2
			ON cr.rOrbitalBaseLinked = ob2.rPlace
			LEFT JOIN player AS pl2
			ON ob2.rPlayer = pl2.id
			LEFT JOIN place AS p2
			ON ob2.rPlace = p2.id

			' . $formatWhere . '
			' . $formatOrder . '
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

		if(empty($valuesArray)) {
			$qr->execute();
		} else {
			$qr->execute($valuesArray);
		}

		while($aw = $qr->fetch()) {
			$cr = new CommercialRoute();
			$cr->setId($aw['id']);
			$cr->setROrbitalBase($aw['rOrbitalBase']);
			$cr->setROrbitalBaseLinked($aw['rOrbitalBaseLinked']);
			$cr->setImageLink($aw['imageLink']);
			$cr->setDistance($aw['distance']);
			$cr->setPrice($aw['price']);
			$cr->setIncome($aw['income']);
			$cr->setDProposition($aw['dProposition']);
			$cr->setDCreation($aw['dCreation']);
			$cr->setStatement($aw['statement']);

			$cr->setBaseName1($aw['baseName1']);
			$cr->baseType1 = $aw['baseType1'];
			$cr->setPlayerId1($aw['playerId1']);
			$cr->setPlayerName1($aw['playerName1']);
			$cr->playerColor1 = $aw['playerColor1'];
			$cr->setAvatar1($aw['avatar1']);
			$cr->setPopulation1($aw['population1']);

			$cr->setBaseName2($aw['baseName2']);
			$cr->baseType2 = $aw['baseType2'];
			$cr->setPlayerId2($aw['playerId2']);
			$cr->setPlayerName2($aw['playerName2']);
			$cr->playerColor2 = $aw['playerColor2'];
			$cr->setAvatar2($aw['avatar2']);
			$cr->setPopulation2($aw['population2']);

			$this->_Add($cr);
		}
	}

	public function emptySession() {
		# empty the session, for player rankings
		$this->_EmptyCurrentSession();
		$this->newSession(FALSE);
	}

	public function add(CommercialRoute $cr) {
		$qr = $this->database->prepare('INSERT INTO
			commercialRoute(rOrbitalBase, rOrbitalBaseLinked, imageLink, distance, price, income, dProposition, dCreation, statement)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)');
		$qr->execute(array(
			$cr->getROrbitalBase(),
			$cr->getROrbitalBaseLinked(),
			$cr->getImageLink(),
			$cr->getDistance(),
			$cr->getPrice(),
			$cr->getIncome(),
			$cr->getDProposition(),
			$cr->getDCreation(),
			$cr->getStatement()
		));

		$cr->setId($this->database->lastInsertId());
		$this->_Add($cr);
	}

	public function save() {
		$routes = $this->_Save();
		foreach ($routes AS $k => $cr) {
			$qr = $this->database->prepare('UPDATE commercialRoute
				SET id = ?,
					rOrbitalBase = ?,
					rOrbitalBaseLinked = ?,
					imageLink = ?,
					distance = ?,
					price = ?,
					income = ?,
					dProposition = ?,
					dCreation = ?,
					statement = ?
				WHERE id = ?
			');
			$qr->execute(array(
				$cr->getId(),
				$cr->getROrbitalBase(), 
				$cr->getROrbitalBaseLinked(), 
				$cr->getImageLink(), 
				$cr->getDistance(), 
				$cr->getPrice(), 
				$cr->getIncome(), 
				$cr->getDProposition(),
				$cr->getDCreation(), 
				$cr->getStatement(), 
				$cr->getId()
			));
		}
	}

	public function deleteById($id) {
		$qr = $this->database->prepare('DELETE FROM commercialRoute WHERE id = ?');
		$qr->execute(array($id));
		
		// suppression de l'objet du manager
		$this->_Remove($id);
		return TRUE;
	}

	public function freezeRoute($color1, $color2) {
		$freeze = TRUE;
		if (!($color1->colorLink[$color2->id] == Color::ENEMY || $color2->colorLink[$color1->id] == Color::ENEMY)) {
			$freeze = FALSE;
		}
		$qr = $this->database->prepare(
			'UPDATE commercialRoute AS cr
				LEFT JOIN orbitalBase AS ob1
					ON cr.rOrbitalBase = ob1.rPlace
				LEFT JOIN player AS pl1
					ON ob1.rPlayer = pl1.id
				LEFT JOIN orbitalBase AS ob2
					ON cr.rOrbitalBaseLinked = ob2.rPlace
				LEFT JOIN player AS pl2
					ON ob2.rPlayer = pl2.id
			SET cr.statement = ?
				WHERE
					((pl1.rColor = ? AND pl2.rColor = ?) OR
					(pl1.rColor = ? AND pl2.rColor = ?)) AND
					cr.statement = ?'
		);

		if ($freeze) {
			$qr->execute(array(CommercialRoute::STANDBY, $color1->id, $color2->id, $color2->id, $color1->id, CommercialRoute::ACTIVE));
		} else {
			$qr->execute(array(CommercialRoute::ACTIVE, $color1->id, $color2->id, $color2->id, $color1->id, CommercialRoute::STANDBY));
		}
	} 
}