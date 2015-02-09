<?php

/**
 * CreditTransactionManager
 *
 * @author Jacky Casas
 * @copyright Asylamba
 *
 * @package Zeus
 * @version 09.02.15
 **/

class CreditTransactionManager extends Manager {
	protected $managerType = '_CreditTransaction';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'ct.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT ct.*
			FROM creditTransaction AS ct
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

		$this->fill($qr);
	}

	protected function fill($qr) {
		while ($aw = $qr->fetch()) {
			$ct = new CreditTransaction();

			$ct->id = $aw['id'];
			$ct->rPlayer = $aw['rPlayer'];
			$ct->rColor = $aw['rColor'];
			$ct->amount = $aw['amount'];
			$ct->dTransaction = $aw['dTransaction'];
			$ct->comment = $aw['comment'];

			$this->_Add($ct);
		}
	}

	public function add(CreditTransaction $ct) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('INSERT INTO
			creditTransaction(rPlayer, rColor, amount, dTransaction, comment)
			VALUES(?, ?, ?, ?, ?)');
		$qr->execute(array(
			$ct->rPlayer,
			$ct->rColor,
			$ct->amount,
			$ct->dTransaction,
			$ct->comment
		));

		$ct->id = $db->lastInsertId();

		$this->_Add($ct);
	}

	public function save() {
		$cts = $this->_Save();

		foreach ($cts AS $ct) {
			$db = DataBase::getInstance();
			$qr = $db->prepare('UPDATE creditTransaction
				SET	id = ?,
					rPlayer = ?,
					rColor = ?,
					amount = ?,
					dTransaction = ?,
					comment = ?
				WHERE id = ?');
			$qr->execute(array(
				$ct->id,
				$ct->rPlayer,
				$ct->rColor,
				$ct->amount,
				$ct->dTransaction,
				$ct->comment,
				$ct->id
			));
		}
	}

	public static function deleteById($id) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('DELETE FROM creditTransaction WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		
		return TRUE;
	}
}
?>