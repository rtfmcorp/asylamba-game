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
		$qr = $db->prepare('SELECT ct.*,
				p1.name AS receiverName,
				p1.avatar AS receiverAvatar,
				p1.status AS receiverStatus,
				p1.rColor AS receiverColor,
				p2.name AS senderName,
				p2.avatar AS senderAvatar,
				p2.status AS senderStatus,
				p2.rColor AS senderColor
			FROM creditTransaction AS ct
			LEFT JOIN player AS p1
				ON ct.rReceiver = p1.id
			LEFT JOIN player AS p2
				ON ct.rSender = p2.id
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
			$ct->rSender = $aw['rSender'];
			$ct->type = $aw['type'];
			$ct->rReceiver = $aw['rReceiver'];
			$ct->amount = $aw['amount'];
			$ct->dTransaction = $aw['dTransaction'];
			$ct->comment = $aw['comment'];

			$ct->senderName = $aw['senderName'];
			$ct->senderAvatar = $aw['senderAvatar'];
			$ct->senderStatus = $aw['senderStatus'];
			$ct->senderColor = $aw['senderColor'];

			if ($ct->type == CreditTransaction::TYP_PLAYER) {
				$ct->receiverName = $aw['receiverName'];
				$ct->receiverAvatar = $aw['receiverAvatar'];
				$ct->receiverStatus = $aw['receiverStatus'];
				$ct->receiverColor = $aw['receiverColor'];
			}

			$this->_Add($ct);
		}
	}

	public function add(CreditTransaction $ct) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('INSERT INTO
			creditTransaction(rSender, type, rReceiver, amount, dTransaction, comment)
			VALUES(?, ?, ?, ?, ?, ?)');
		$qr->execute(array(
			$ct->rSender,
			$ct->type,
			$ct->rReceiver,
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
					rSender = ?,
					type = ?,
					rReceiver = ?,
					amount = ?,
					dTransaction = ?,
					comment = ?
				WHERE id = ?');
			$qr->execute(array(
				$ct->id,
				$ct->rSender,
				$ct->type,
				$ct->rReceiver,
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