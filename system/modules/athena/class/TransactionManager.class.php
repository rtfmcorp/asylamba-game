<?php

/**
 * TransactionManager
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Athena
 * @version 19.11.13
 **/

class TransactionManager extends Manager {
	protected $managerType = '_Transaction';

	public function load($where = array(), $order = array(), $limit = array(), $typeToLoad = 0) {
		switch ($typeToLoad) {
			case Transaction::TYP_RESOURCE :
				loadTransactions($where, $order, $limit);
				break;
			case Transaction::TYP_SHIP :
				loadTransactions($where, $order, $limit);
				break;
			case Transaction::TYP_COMMANDER :
				loadCommanderTransactions($where, $order, $limit);
				break;
			default :
				loadTransactions($where, $ordre, $limit);
				break;
		}
	}

	private function loadTransactions($where, $order, $limit) {
		$formatWhere = Utils::arrayToWhere($where, 't.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT t.*
			FROM transaction AS t
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
			$t = new Transaction();

			$t->id = $aw['id'];
			$t->rPlayer = $aw['rPlayer'];
			$t->rPlace = $aw['rPlace'];
			$t->type = $aw['type'];
			$t->quantity = $aw['quantity'];
			$t->identifier = $aw['identifier'];
			$t->price = $aw['price'];
			$t->shipQuantity = $aw['commercialShipQuantity'];
			$t->statement = $aw['statement'];
			$t->dPublication = $aw['dPublication'];
			$t->dValidation = $aw['dValidation'];
			$t->currentRate = $aw['currentRate'];

			$currentT = $this->_Add($t);
		}
	}

	private function loadCommanderTransactions($where, $order, $limit) {
		$formatWhere = Utils::arrayToWhere($where, 't.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT t.*, c.name, c.level, c.palmares
			FROM transaction AS t
			INNER JOIN commander AS c 
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

		if(empty($valuesArray)) {
			$qr->execute();
		} else {
			$qr->execute($valuesArray);
		}

		while($aw = $qr->fetch()) {
			$t = new CommanderTransaction();

			$t->id = $aw['id'];
			$t->rPlayer = $aw['rPlayer'];
			$t->rPlace = $aw['rPlace'];
			$t->type = $aw['type'];
			$t->quantity = $aw['quantity'];
			$t->identifier = $aw['identifier'];
			$t->price = $aw['price'];
			$t->shipQuantity = $aw['commercialShipQuantity'];
			$t->statement = $aw['statement'];
			$t->dPublication = $aw['dPublication'];
			$t->dValidation = $aw['dValidation'];
			$t->currentRate = $aw['currentRate'];

			$t->name = $aw['name'];
			$t->level = $aw['level'];
			$t->palmares = $aw['palmares'];

			$currentT = $this->_Add($t);
		}
	}

	public function getExchangeRate($transactionType) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT currentRate
			FROM transaction 
			WHERE type = ? AND statement = ?
			ORDER BY dValidation DESC 
			LIMIT 1');

		$qr->execute(array($transactionType, Transaction::ST_COMPLETED));
		$aw = $qr->fetch();
		return $aw['currentRate'];
	}

	public function add(Transaction $t) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('INSERT INTO
			transaction(rPlayer, rPlace, type, quantity, identifier, price, commercialShipQuantity, statement, dPublication, dValidation, currentRate)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
		$qr->execute(array(
			$t->rPlayer,
			$t->rPlace,
			$t->type,
			$t->quantity,
			$t->identifier,
			$t->price,
			$t->commercialShipQuantity,
			$t->statement,
			$t->dPublication,
			$t->dValidation,
			$t->currentRate
		));

		$t->id = $db->lastInsertId();

		$this->_Add($t);
	}

	public function save() {
		$transactions = $this->_Save();

		foreach ($transactions AS $t) {
			$db = DataBase::getInstance();
			$qr = $db->prepare('UPDATE transaction
				SET	id = ?,
					rPlayer = ?,
					rPlace = ?,
					type = ?,
					quantity = ?,
					identifier = ?,
					price = ?,
					commercialShipQuantity = ?,
					statement = ?,
					dPublication = ?,
					dValidation = ?,
					currentRate = ?
				WHERE id = ?');
			$qr->execute(array(
				$t->id,
				$t->rPlayer,
				$t->rPlace,
				$t->type,
				$t->quantity,
				$t->identifier,
				$t->price,
				$t->commercialShipQuantity,
				$t->statement,
				$t->dPublication,
				$t->dValidation,
				$t->currentRate,
				$t->id
			));
		}
	}

	public function deleteById($id) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('DELETE FROM transaction WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		
		return TRUE;
	}
}
?>