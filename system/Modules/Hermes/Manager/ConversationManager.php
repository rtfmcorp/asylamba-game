<?php

namespace Asylamba\Modules\Hermes\Manager;

use Asylamba\Classes\Worker\Manager;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Database\Database;
use Asylamba\Classes\Worker\ASM;

use Asylamba\Modules\Hermes\Model\Conversation;

class ConversationManager extends Manager {
	protected $managerType ='_Conversation';

	public function load($where = array(), $order = array(), $limit = array()) {
		$db = Database::getInstance();
		$qr = $db->prepare('SELECT c.*
			FROM conversation AS c
			LEFT JOIN conversationUser AS cu
				ON cu.rConversation = c.id
			' . Utils::arrayToWhere($where) .'
			GROUP BY c.id
			' . Utils::arrayToOrder($order) .'
			' . Utils::arrayToLimit($limit) 
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

		$conversationIds = array(0);

		foreach ($aws as $aw) {
			$conversationIds[] = $aw['id'];
		}

		$S_CUM = ASM::$cum->getCurrentSession();
		ASM::$cum->newSession();
		ASM::$cum->load(array('c.rConversation' => $conversationIds));

		foreach ($aws AS $aw) {
			$conv = new Conversation();

			$conv->id = $aw['id'];
			$conv->title = $aw['title'];
			$conv->messages = $aw['messages'];
			$conv->type = $aw['type'];
			$conv->dCreation = $aw['dCreation'];
			$conv->dLastMessage = $aw['dLastMessage'];

			for ($i = 0; $i < ASM::$cum->size(); $i++) {
				if (ASM::$cum->get($i)->rConversation == $conv->id) {
					$conv->players[] = ASM::$cum->get($i);
				}
			}
			
			$this->_Add($conv);
		}

		ASM::$cum->changeSession($S_CUM);
	}

	public function save() {
		$db = Database::getInstance();

		$convs = $this->_Save();

		foreach ($convs AS $conv) {
			$qr = $db->prepare('UPDATE conversation
				SET
					title = ?,
					messages = ?,
					type = ?,
					dCreation = ?,
					dLastMessage = ?
				WHERE id = ?');
			$aw = $qr->execute(array(
					$conv->title,
					$conv->messages,
					$conv->type,
					$conv->dCreation,
					$conv->dLastMessage,
					$conv->id
				)
			);
		}
	}

	public function add($conv) {
		$db = Database::getInstance();

		$qr = $db->prepare('INSERT INTO conversation
			SET title = ?,
				messages = ?,
				type = ?,
				dCreation = ?,
				dLastMessage = ?'
		);

		$aw = $qr->execute(array(
				$conv->title,
				$conv->messages,
				$conv->type,
				Utils::now(),
				Utils::now()
		));

		$conv->id = $db->lastInsertId();
		$this->_Add($conv);

		return $conv->id;
	}

	public function deleteById($id) {
		$db = Database::getInstance();
		$qr = $db->prepare('DELETE FROM conversation WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		return TRUE;
	}

	public static function count($where = array()) {
		return 0;
	}
}
