<?php

/**
 * Message Manager
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Hermes
 * @update 20.05.13
*/

class MessageManager extends Manager {
	protected $managerType = '_Message';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'm.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT 
				m.*,
				p1.name AS writerName,
				p1.rColor AS writerColor,
				p1.avatar AS writerAvatar,
				p2.name AS readerName,
				p2.rColor AS readerColor,
				p2.avatar AS readerAvatar
			FROM message AS m
			LEFT JOIN player AS p1
				ON m.rPlayerWriter = p1.id
			LEFT JOIN player AS p2
				ON m.rPlayerReader = p2.id
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
			$m = new Message();

			$m->setId($aw['id']);
			$m->setThread($aw['thread']);
			$m->setRPlayerWriter($aw['rPlayerWriter']);
			$m->setRPlayerReader($aw['rPlayerReader']);
			$m->setDSending($aw['dSending']);
			$m->setContent($aw['content']);
			$m->setReaded($aw['readed']);
			$m->setWriterStatement($aw['writerStatement']);
			$m->setReaderStatement($aw['readerStatement']);

			$m->setWriterName($aw['writerName']);
			$m->setWriterColor($aw['writerColor']);
			$m->setWriterAvatar($aw['writerAvatar']);
			$m->setReaderName($aw['readerName']);
			$m->setReaderColor($aw['readerColor']);
			$m->setReaderAvatar($aw['readerAvatar']);

			$this->_Add($m);
		}
	}

	public function loadByRequest($query, $args = array()) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT 
				m.*,
				p1.name AS writerName,
				p1.rColor AS writerColor,
				p1.avatar AS writerAvatar,
				p2.name AS readerName,
				p2.rColor AS readerColor,
				p2.avatar AS readerAvatar
			FROM message AS m
			LEFT JOIN player AS p1
				ON m.rPlayerWriter = p1.id
			LEFT JOIN player AS p2
				ON m.rPlayerReader = p2.id
			' . $query
		);

		$qr->execute($args);

		while($aw = $qr->fetch()) {
			$m = new Message();

			$m->setId($aw['id']);
			$m->setThread($aw['thread']);
			$m->setRPlayerWriter($aw['rPlayerWriter']);
			$m->setRPlayerReader($aw['rPlayerReader']);
			$m->setDSending($aw['dSending']);
			$m->setContent($aw['content']);
			$m->setReaded($aw['readed']);
			$m->setWriterStatement($aw['writerStatement']);
			$m->setReaderStatement($aw['readerStatement']);

			$m->setWriterName($aw['writerName']);
			$m->setWriterColor($aw['writerColor']);
			$m->setWriterAvatar($aw['writerAvatar']);
			$m->setReaderName($aw['readerName']);
			$m->setReaderColor($aw['readerColor']);
			$m->setReaderAvatar($aw['readerAvatar']);

			$this->_Add($m);
		}
	}

	public function add(Message $m) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('INSERT INTO
			message(thread, rPlayerWriter, rPlayerReader, dSending, content, readed, writerStatement, readerStatement)
			VALUES(?, ?, ?, ?, ?, ?, ?, ?)');
		$qr->execute(array(
			$m->getThread(),
			$m->getRPlayerWriter(),
			$m->getRPlayerReader(),
			$m->getDSending(),
			$m->getContent(),
			$m->getReaded(),
			$m->getWriterStatement(),
			$m->getReaderStatement()
		));

		$m->setId($db->lastInsertId());

		$this->_Add($m);
	}

	public function save() {
		$messages = $this->_Save();

		foreach ($messages AS $m) {
			$db = DataBase::getInstance();
			$qr = $db->prepare('UPDATE message
				SET	id = ?,
					thread = ?,
					rPlayerWriter = ?,
					rPlayerReader = ?,
					dSending = ?,
					content = ?,
					readed = ?,
					writerStatement = ?,
					readerStatement = ?
				WHERE id = ?');
			$qr->execute(array(
				$m->getId(),
				$m->getThread(),
				$m->getRPlayerWriter(),
				$m->getRPlayerReader(),
				$m->getDSending(),
				$m->getContent(),
				$m->getReaded(),
				$m->getWriterStatement(),
				$m->getReaderStatement(),
				$m->getId()
			));
		}
	}

	public function deleteById($id) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('DELETE FROM message WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		return TRUE;
	}
}
?>