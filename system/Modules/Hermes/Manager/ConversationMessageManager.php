<?php

namespace Asylamba\Modules\Hermes\Manager;

use Asylamba\Classes\Worker\Manager;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Database\Database;

use Asylamba\Modules\Hermes\Model\ConversationMessage;

class ConversationMessageManager extends Manager
{
	protected $managerType ='_ConversationMessage';

	/**
	 * @param Database $database
	 */
	public function __construct(Database $database)
	{
		parent::__construct($database);
	}
	
	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where);
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$qr = $this->database->prepare('SELECT c.*,
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
		$messages = $this->_Save();

		foreach ($messages AS $message) {
			$qr = $this->database->prepare('UPDATE conversationMessage
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
		$qr = $this->database->prepare('INSERT INTO conversationMessage
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

		$message->id = $this->database->lastInsertId();
		$this->_Add($message);

		return $message->id;
	}

	public function deleteById($id) {
		$qr = $this->database->prepare('DELETE FROM conversationMessage WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		return TRUE;
	}

	public static function count($where = array()) {
		return 0;
	}
}
