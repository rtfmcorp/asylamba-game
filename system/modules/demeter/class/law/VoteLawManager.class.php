<?php

/**
 * VoteLawLaw Manager
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Demeter
 * @update 29.09.14
*/

class VoteLawManager extends Manager {
	protected $managerType ='_VoteLaw';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'v.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT v.*
			FROM voteLaw AS v
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

		foreach($aw AS $awVoteLaw) {
			$voteLaw = new VoteLaw();

			$voteLaw->id = $awVoteLaw['id'];
			$voteLaw->rCandidate = $awVoteLaw['rLaw'];
			$voteLaw->rPlayer = $awVoteLaw['rPlayer'];
			$voteLaw->relection = $awVoteLaw['dVotation'];

			$this->_Add($voteLaw);
		}
	}

	public function save() {
		$db = DataBase::getInstance();

		$voteLaws = $this->_Save();

	foreach ($voteLaws AS $voteLaw) {


		$qr = $db->prepare('UPDATE voteLaw
			SET
				rLaw = ?,
				rPlayer = ?,
				dVotation = ?
			WHERE id = ?');
		$aw = $qr->execute(array(
				$voteLaw->rLaw,
				$voteLaw->rPlayer,
				$voteLaw->dVotation,
				$voteLaw->id

			));
		}
	}

	public function add($newVoteLaw) {
		$db = DataBase::getInstance();

		$qr = $db->prepare('INSERT INTO voteLaw
			SET
				rLaw = ?,
				rPlayer = ?,
				dVotation = ?');

			$aw = $qr->execute(array(
				$newVoteLaw->rLaw,
				$newVoteLaw->rPlayer,
				$newVoteLaw->dVotation
				));

		$newVoteLaw->id = $db->lastInsertId();

		$this->_Add($newVoteLaw);

		return $newVoteLaw->id;
	}

	public function deleteById($id) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('DELETE FROM voteLaw WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		return TRUE;
	}
}
