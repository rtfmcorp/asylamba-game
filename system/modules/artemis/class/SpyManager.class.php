<?php

/**
 * SpyManager
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package artemis
 * @version 05.13
 **/

class SpyManager extends Manager {
	protected $managerType = '_Spy';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 's.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT s.*,
			p.name AS pName,
			p.rColor AS pColor
			FROM spy AS s
			LEFT JOIN player AS p
				ON p.id = s.rPlayer

			' . $formatWhere . '
			' . $formatOrder . '
			' . $formatLimit
		);

		foreach($where AS $v) {
			if (is_array($v)) {
				foreach ($v as $s) {
					$valuesArray[] = $s;
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

		$awSpies = $qr->fetchAll();

		foreach($awSpies AS $awSpy) {
			$s = new Spy();

			$s->id = $awSpy['id'];
			$s->name = $awSpy['name'];
			$s->rPlayer = $awSpy['rPlayer'];
			$s->rSystem= $awSpy['rSystem'];
			$s->sexe = $awSpy['sexe'];
			$s->age = $awSpy['age'];
			$s->level = $awSpy['level'];
			$s->avatar = $awSpy['avatar'];
			$s->statement = $awSpy['statement'];
			$s->experience = $awSpy['experience'];
			$s->comment = $awSpy['comment'];
			$s->rSystemDestination = $awSpy['rSystemDestination'];
			$s->arrivalDate = $awSpy['arrivalDate'];
			$s->uExperience = $awSpy['uExperience'];
			$s->dCreation = $awSpy['dCreation'];
			$s->dDeath = $awSpy['dDeath'];
			$s->playerName = $awSpy['pName'];
			$s->playerColor = $awSpy['pColor'];

			$s->executeUMethode();
			
			$this->_Add($s);
		}
	}


	public function add(Spy $s) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('INSERT INTO spy
			SET
				name = ?,
				rPlayer = ?,
				rSystem = ?,
				age = ?,
				level = ?,
				avatar = ?,
				statement = ?,
				experience = ?,
				comment = ?,
				rSystemDestination = ?,
				arrivalDate = ?,
				uExperience = ?,
				dDeath = ?
			');
		$qr->execute(array(
			$s->name,
			$s->rPlayer,
			$s->rSystem,
			$s->age,
			$s->level,
			$s->avatar,
			$s->statement,
			$s->experience,
			$s->comment,
			$s->rSystemDestination,
			$s->arrivalDate,
			$s->uExperience,
			$s->dDeath
		));

		$p->setId($db->lastInsertId());

		$this->_Add($p);
	}

	public function save() {
		$spies = $this->_Save();

		foreach ($spies AS $spy) {
			$db = DataBase::getInstance();
			$qr = $db->prepare('UPDATE spy
				SET
					name = ?,
					rPlayer = ?,
					rSystem = ?,
					age = ?,
					level = ?,
					avatar = ?,
					statement = ?,
					experience = ?,
					comment = ?,
					rSystemDestination = ?,
					arrivalDate = ?,
					uExperience = ?,
					dDeath = ?
				WHERE id = ?');
			$qr->execute(array(
				$spy->name,
				$spy->rPlayer,
				$spy->rSystem,
				$spy->age,
				$spy->level,
				$spy->avatar,
				$spy->statement,
				$spy->experience,
				$spy->comment,
				$spy->rSystemDestination,
				$spy->arrivalDate,
				$spy->uExperience,
				$spy->dDeath,
				$spy->id
			));
		}
	}
}