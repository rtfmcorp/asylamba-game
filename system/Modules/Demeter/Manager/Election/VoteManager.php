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
namespace Asylamba\Modules\Demeter\Manager\Election;

use Asylamba\Classes\Worker\Manager;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Database\Database;
use Asylamba\Modules\Demeter\Model\Election\Vote;

class VoteManager extends Manager {
	protected $managerType ='_Vote';

	/**
	 * @param Database $database
	 */
	public function __construct(Database $database) {
		parent::__construct($database);
	}
	
	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'v.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$qr = $this->database->prepare('SELECT v.*
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
			$vote->relection = $awVote['rElection'];
			$vote->dVotation = $awVote['dVotation'];

			$this->_Add($vote);
		}
	}

	public function save() {
		$votes = $this->_Save();

		foreach ($votes AS $vote) {

			$qr = $this->database->prepare('UPDATE vote
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
		$qr = $this->database->prepare('INSERT INTO vote
			SET
				rCandidate = ?,
				rPlayer = ?,
				rElection = ?,
				dVotation = ?');

		$aw = $qr->execute(array(
			$newVote->rCandidate,
			$newVote->rPlayer,
			$newVote->rElection,
			$newVote->dVotation
		));

		$newVote->id = $this->database->lastInsertId();

		$this->_Add($newVote);

		return $newVote->id;
	}

	public function deleteById($id) {
		$qr = $this->database->prepare('DELETE FROM vote WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		return TRUE;
	}
}
