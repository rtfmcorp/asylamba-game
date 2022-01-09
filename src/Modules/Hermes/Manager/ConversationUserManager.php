<?php

namespace App\Modules\Hermes\Manager;

use App\Classes\Worker\Manager;
use App\Classes\Database\Database;
use App\Classes\Library\Utils;
use App\Modules\Hermes\Model\ConversationUser;

class ConversationUserManager extends Manager
{
	protected $managerType ='_ConversationUser';

	public function __construct(Database $database)
	{
		parent::__construct($database);
	}
	
	public function load($where = array(), $order = array(), $limit = array()) {
		$qr = $this->database->prepare('SELECT c.*,
				p.id AS playerId,
				p.rColor AS playerColor,
				p.name AS playerName,
				p.avatar AS playerAvatar,
				p.status AS playerStatus
			FROM conversationUser AS c
			LEFT JOIN player AS p
				ON c.rPlayer = p.id
			' . Utils::arrayToWhere($where) .'
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

		foreach ($aws AS $aw) {
			$user = new ConversationUser();
			$user->id = $aw['id'];
			$user->rConversation = $aw['rConversation'];
			$user->rPlayer = $aw['playerId'];

			$user->convPlayerStatement = $aw['playerStatement'];
			$user->convStatement = $aw['convStatement'];
			$user->dLastView = $aw['dLastView'];

			$user->playerColor = $aw['playerColor'];
			$user->playerName = $aw['playerName'];
			$user->playerAvatar = $aw['playerAvatar'];
			$user->playerStatus = $aw['playerStatus'];

			$this->_Add($user);
		}
	}

	public function save() {
		$userConvs = $this->_Save();

		foreach ($userConvs AS $userConv) {
			$qr = $this->database->prepare('UPDATE conversationUser
				SET
					rConversation = ?,
					rPlayer = ?,
					playerStatement = ?,
					convStatement = ?,
					dLastView = ?
				WHERE id = ?');
			$aw = $qr->execute(array(
					$userConv->rConversation,
					$userConv->rPlayer,
					$userConv->convPlayerStatement,
					$userConv->convStatement,
					$userConv->dLastView,
					$userConv->id
				)
			);
		}
	}

	public function add($userConv) {
		$qr = $this->database->prepare('INSERT INTO conversationUser
			SET rConversation = ?,
				rPlayer = ?,
				playerStatement = ?,
				convStatement = ?,
				dLastView = ?'
		);

		$aw = $qr->execute(array(
				$userConv->rConversation,
				$userConv->rPlayer,
				$userConv->convPlayerStatement,
				$userConv->convStatement,
				$userConv->dLastView
		));

		$userConv->id = $this->database->lastInsertId();
		$this->_Add($userConv);

		return $userConv->id;
	}

	public function deleteById($id) {
		$qr = $this->database->prepare('DELETE FROM conversationUser WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		return TRUE;
	}

	public static function count($where = array()) {
		return 0;
	}
}
