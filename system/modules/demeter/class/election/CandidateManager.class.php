<?php

/**
 * Candidate Manager
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Demeter
 * @update 06.10.13
*/

class CandidateManager extends Manager {
	protected $managerType ='_Candidate';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'c.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT c.*,
			p.name AS pName,
			p.avatar AS pAvatar,
			p.factionPoint AS pFactionPoint,
			p.status AS pStatus
			FROM candidate AS c
			LEFT JOIN player AS p
				ON p.id = c.rPlayer
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

		foreach($aw AS $awCandidate) {
			$candidate = new Candidate();

			$candidate->id = $awCandidate['id'];
			$candidate->rElection = $awCandidate['rElection'];
			$candidate->rPlayer = $awCandidate['rPlayer'];
			$candidate->chiefChoice = $awCandidate['chiefChoice'];
			$candidate->treasurerChoice = $awCandidate['treasurerChoice'];
			$candidate->warlordChoice = $awCandidate['warlordChoice'];
			$candidate->ministerChoice = $awCandidate['ministerChoice'];
			$candidate->program = $awCandidate['program'];
			$candidate->dPresentation = $awCandidate['dPresentation'];

			# Jointure
			$candidate->name = $awCandidate['pName'];
			$candidate->avatar = $awCandidate['pAvatar'];
			$candidate->factionPoint = $awCandidate['pFactionPoint'];
			$candidate->status = $awCandidate['pStatus'];
			
			$this->_Add($candidate);
		}
	}

	public function save() {
		$db = DataBase::getInstance();

		$candidates = $this->_Save();

	foreach ($candidates AS $candidate) {


		$qr = $db->prepare('UPDATE candidate
			SET
				rElection = ?,
				rPlayer = ?,
				chiefChoice = ?,
				treasurerChoice = ?,
				warlordChoice = ?,
				ministerChoice = ?,
				dPresentation = ?
			WHERE id = ?');
		$aw = $qr->execute(array(
				$candidate->rElection,
				$candidate->rPlayer,
				$candidate->chiefChoice,
				$candidate->treasurerChoice,
				$candidate->warlordChoice,
				$candidate->ministerChoice,
				$candidate->dPresentation,
				$candidate->id
			));
		}
	}

	public function add($newCandidate) {
		$db = DataBase::getInstance();

		$qr = $db->prepare('INSERT INTO candidate
			SET
				rElection = ?,
				rPlayer = ?,
				chiefChoice = ?,
				treasurerChoice = ?,
				warlordChoice = ?,
				ministerChoice = ?,
				program = ?,
				dPresentation = ?');

			$aw = $qr->execute(array(
				$newCandidate->rElection,
				$newCandidate->rPlayer,
				$newCandidate->chiefChoice,
				$newCandidate->treasurerChoice,
				$newCandidate->warlordChoice,
				$newCandidate->ministerChoice,
				$newCandidate->program,
				$newCandidate->dPresentation
				));

		$newCandidate->id = $db->lastInsertId();

		$this->_Add($newCandidate);

		return $newCandidate->id;
	}

	public function deleteById($id) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('DELETE FROM candidate WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		return TRUE;
	}
}
