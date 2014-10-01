<?php

/**
 * law Manager
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Demeter
 * @update 29.09.14
*/

class LawManager extends Manager {
	protected $managerType ='_Law';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'l.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT l.*
			FROM law AS l
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

		$aw = $qr->fetchAll();
		$qr->closeCursor();

		foreach($aw AS $awLaw) {
			$law = new Law();

			$law->id = $awLaw['id'];
			$law->rColorCreator = $awLaw['rColorCreator'];
			$law->rColorTarget = $awLaw['rColorTarget'];
			$law->type = $awLaw['type'];
			$law->statement = $awLaw['statement'];
			$law->duration = $awLaw['duration'];
			$law->dCreation = $awLaw['dCreation'];

			$law->uLaw();
			
			$this->_Add($law);
		}
	}

	public function save() {
		$db = DataBase::getInstance();

		$laws = $this->_Save();

	foreach ($laws AS $law) {


		$qr = $db->prepare('UPDATE law
			SET
				rColorCreator = ?,
				rColorTarget = ?,
				type = ?,
				statement = ?,
				duration = ?,
				dCreation = ?
			WHERE id = ?');
		$aw = $qr->execute(array(
			$law->rColorCreator,
			$law->rColorTarget,
			$law->type,
			$law->statement,
			$law->duration,
			$law->dCreation,
			$law->id
			));
		}
	}

	public function add($newLaw) {
		$db = DataBase::getInstance();

		$qr = $db->prepare('INSERT INTO election
			SET
				rColorCreator = ?
				rColorTarget = ?
				type = ?
				statement = ?
				duration = ?
				dCreation = ?');

			$aw = $qr->execute(array(
				$law->rColorCreator,
				$law->rColorTarget,
				$law->type,
				$law->statement,
				$law->duration,
				$law->dCreation
				));

		$newLaw->id = $db->lastInsertId();

		$this->_Add($newLaw);

		return $newLaw->id;
	}

	public function deleteById($id) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('DELETE FROM law WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		return TRUE;
	}
}
