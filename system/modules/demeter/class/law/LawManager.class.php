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
		$qr = $db->prepare('SELECT l.*,
				(SELECT COUNT(v.id) FROM voteLaw AS v WHERE rLaw = l.id AND vote = 1) AS forVote,
				(SELECT COUNT(v.id) FROM voteLaw AS v WHERE rLaw = l.id AND vote = 0) AS againstVote
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
			$law->rColor = $awLaw['rColor'];
			$law->type = $awLaw['type'];
			$law->options = unserialize($awLaw['options']);
			$law->statement = $awLaw['statement'];
			$law->dEndVotation = $awLaw['dEndVotation'];
			$law->dEnd = $awLaw['dEnd'];
			$law->dCreation = $awLaw['dCreation'];

			$law->forVote = $awLaw['forVote'];
			$law->againstVote = $awLaw['againstVote'];
			
			$this->_Add($law);
		}
	}

	public function save() {
		$db = DataBase::getInstance();

		$laws = $this->_Save();

		foreach ($laws AS $law) {

			$qr = $db->prepare('UPDATE law
				SET
					rColor = ?,
					type = ?,
					statement = ?,
					dEnd = ?,
					dEndVotation = ?,
					dCreation = ?
				WHERE id = ?');
			$aw = $qr->execute(array(
				$law->rColor,
				$law->type,
				$law->statement,
				$law->dEnd,
				$law->dEndVotation,
				$law->dCreation,
				$law->id
			));
		}
	}

	public function add($newLaw) {
		$db = DataBase::getInstance();

		$qr = $db->prepare('INSERT INTO law
			SET
				rColor = ?,
				type = ?,
				statement = ?,
				options = ?,
				dEnd = ?,
				dEndVotation = ?,
				dCreation = ?');

			$aw = $qr->execute(array(
				$newLaw->rColor,
				$newLaw->type,
				$newLaw->statement,
				$newLaw->options,
				$newLaw->dEnd,
				$newLaw->dEndVotation,
				$newLaw->dCreation
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
