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
namespace Asylamba\Modules\Demeter\Manager\Election;

use Asylamba\Classes\Worker\Manager;
use Asylamba\Classes\Database\Database;
use Asylamba\Classes\Library\Utils;

use Asylamba\Modules\Demeter\Model\Election\Candidate;

class CandidateManager extends Manager {
	protected $managerType ='_Candidate';

	public function __construct(Database $database) {
		parent::__construct($database);
	}
	
	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'c.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$qr = $this->database->prepare('SELECT c.*,
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
		$candidates = $this->_Save();

		foreach ($candidates AS $candidate) {

			$qr = $this->database->prepare('UPDATE candidate
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
		$qr = $this->database->prepare('INSERT INTO candidate
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

		$newCandidate->id = $this->database->lastInsertId();

		$this->_Add($newCandidate);

		return $newCandidate->id;
	}

	public function deleteById($id) {
		$qr = $this->database->prepare('DELETE FROM candidate WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		return TRUE;
	}
}
