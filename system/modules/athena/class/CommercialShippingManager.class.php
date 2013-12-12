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
		$qr = $db->prepare('SELECT cs.*
			FROM commercialShipping AS cs
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