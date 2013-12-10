<?php

/**
 * Vote Manager
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Demeter
 * @update 06.10.13
*/

class VoteManager extends Manager {
	protected $managerType ='_Votee';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'v.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT v.*
			FROM vote AS v
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

		foreach($aw AS $awVote) {
			$vote = new Vote();

			$vote->id = $awVote['id'];
			$vote->rCandidate = $awVote['rCandidate'];
			$vote->rPlayer = $awVote['rPlayer'];
			$vote->dVotation = $awVote['dVotation'];

			$this->_Add($vote);
		}
	}

	public function save() {
		$db = DataBase::getInstance();

		$votes = $this->_Save();

	foreach ($votes AS $vote) {


		$qr = $db->prepare('UPDATE vote
			SET
				rCandidate = ?,
				rPlayer = ?,
				dVotation = ?
			WHERE id = ?');
		$aw = $qr->execute(array(
				$vote->rCandidate,
				$vote->rPlayer,
				$vote->dVotation,
				$vote->id

			));
		}
	}

	public function add($newVote) {
		$db = DataBase::getInstance();

		$qr = $db->prepare('INSERT INTO vote
			SET
				rCandidate = ?,
				rPlayer = ?,
				dVotation = ?');

			$aw = $qr->execute(array(
				$newVote->rCandidate,
				$newVote->rPlayer,
				utils::now()
				));

		$newVote->id = $db->lastInsertId();

		$this->_Add($newVote);

		return $newVote->id;
	}

	public function deleteById($id) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('DELETE FROM vote WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		return TRUE;
	}
}
