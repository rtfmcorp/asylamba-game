<?php
class ConversationMessageManager extends Manager {
	protected $managerType ='_ConversationMessage';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where);
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT c.*,
				p.rColor AS playerColor,
				p.name AS playerName,
				p.avatar AS playerAvatar,
				p.status AS playerStatus
			FROM conversationMessage AS c
			LEFT JOIN player AS p
				ON c.rPlayer = p.id
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

		$aws = $qr->fetchAll();
		$qr->closeCursor();

		foreach ($aws AS $aw) {
			$message = new ConversationMessage();

			$message->id = $aw['id'];
			$message->rConversation = $aw['rConversation'];
			$message->rPlayer = $aw['rPlayer'];
			$message->type = $aw['type'];
			$message->content = $aw['content'];
			$message->dCreation = $aw['dCreation'];
			$message->dLastModification = $aw['dLastModification'];

			$message->playerColor = $aw['playerColor'];
			$message->playerName = $aw['playerName'];
			$message->playerAvatar = $aw['playerAvatar'];
			$message->playerStatus = $aw['playerStatus'];
			
			$this->_Add($message);
		}
	}

	public function save() {
		$db = DataBase::getInstance();

		$messages = $this->_Save();

		foreach ($messages AS $message) {
			$qr = $db->prepare('UPDATE conversationMessage
				SET
					rConversation = ?,
					rPlayer = ?,
					type = ?,
					content = ?,
					dCreation = ?,
					dLastModification = ?
				WHERE id = ?');
			$aw = $qr->execute(array(
					$message->rConversation,
					$message->rPlayer,
					$message->type,
					$message->content,
					$message->dCreation,
					$message->dLastModification,
					$message->id
				)
			);
		}
	}

	public function add($message) {
		$db = DataBase::getInstance();

		$qr = $db->prepare('INSERT INTO conversationMessage
			SET rConversation = ?,
				rPlayer = ?,
				type = ?,
				content = ?,
				dCreation = ?,
				dLastModification = ?'
		);

		$aw = $qr->execute(array(
				$message->rConversation,
				$message->rPlayer,
				$message->type,
				$message->content,
				$message->dCreation,
				$message->dLastModification
		));

		$conv->id = $db->lastInsertId();
		$this->_Add($conv);

		return $conv->id;
	}

	public function deleteById($id) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('DELETE FROM conversationMessage WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		return TRUE;
	}

	public static function count($where = array()) {
		return 0;
	}
}
