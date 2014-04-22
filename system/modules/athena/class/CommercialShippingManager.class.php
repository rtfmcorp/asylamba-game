<?php

/**
 * CommercialShippingManager
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Athena
 * @version 19.11.13
 **/

class CommercialShippingManager extends Manager {
	protected $managerType = '_CommercialShipping';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'cs.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT cs.*, 
			p1.rSystem AS rSystem1, p1.position AS position1, s1.xPosition AS xSystem1, s1.yPosition AS ySystem1,
			p2.rSystem AS rSystem2, p2.position AS position2, s2.xPosition AS xSystem2, s2.yPosition AS ySystem2,
			t.type AS typeOfTransaction, t.quantity AS quantity, t.identifier AS identifier,
			c.avatar AS commanderAvatar, c.name AS commanderName, c.level AS commanderLevel
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
				ON t.identifier = c.id
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

		if (empty($valuesArray)) {
			$qr->execute();
		} else {
			$qr->execute($valuesArray);
		}

		while ($aw = $qr->fetch()) {
			$cs = new CommercialShipping();

			$cs->id = $aw['id'];
			$cs->rPlayer = $aw['rPlayer'];
			$cs->rBase = $aw['rBase'];
			$cs->rBaseDestination = $aw['rBaseDestination'];
			$cs->rTransaction = $aw['rTransaction'];
			$cs->resourceTransported = $aw['resourceTransported'];
			$cs->shipQuantity = $aw['shipQuantity'];
			$cs->dDeparture = $aw['dDeparture'];
			$cs->dArrival = $aw['dArrival'];
			$cs->statement = $aw['statement'];

			$cs->baseRSystem = $aw['rSystem1'];
			$cs->basePosition = $aw['position1'];
			$cs->baseXSystem = $aw['xSystem1'];
			$cs->baseYSystem = $aw['ySystem1'];

			$cs->destinationRSystem = $aw['rSystem2'];
			$cs->destinationPosition = $aw['position2'];
			$cs->destinationXSystem = $aw['xSystem2'];
			$cs->destinationYSystem = $aw['ySystem2'];

			$cs->typeOfTransaction = $aw['typeOfTransaction'];
			$cs->quantity = $aw['quantity'];
			$cs->identifier = $aw['identifier'];
			$cs->commanderAvatar = $aw['commanderAvatar'];
			$cs->commanderName = $aw['commanderName'];
			$cs->commanderLevel = $aw['commanderLevel'];

			$currentCS = $this->_Add($cs);
		}
	}

	public function add(CommercialShipping $cs) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('INSERT INTO
			commercialShipping(rPlayer, rBase, rBaseDestination, rTransaction, resourceTransported, shipQuantity, dDeparture, dArrival, statement)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?)');
		$qr->execute(array(
			$cs->rPlayer,
			$cs->rBase,
			$cs->rBaseDestination,
			$cs->rTransaction,
			$cs->resourceTransported,
			$cs->shipQuantity,
			$cs->dDeparture,
			$cs->dArrival,
			$cs->statement
		));

		$cs->id = $db->lastInsertId();

		$this->_Add($cs);
	}

	public function save() {
		$commercialShippings = $this->_Save();

		foreach ($commercialShippings AS $cs) {
			$db = DataBase::getInstance();
			$qr = $db->prepare('UPDATE commercialShipping
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
				WHERE id = ?');
			$qr->execute(array(
				$cs->id,
				$cs->rPlayer,
				$cs->rBase,
				$cs->rBaseDestination,
				$cs->rTransaction,
				$cs->resourceTransported,
				$cs->shipQuantity,
				$cs->dDeparture,
				$cs->dArrival,
				$cs->statement,
				$cs->id
			));
		}
	}

	public function deleteById($id) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('DELETE FROM commercialShipping WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		
		return TRUE;
	}
}
?>