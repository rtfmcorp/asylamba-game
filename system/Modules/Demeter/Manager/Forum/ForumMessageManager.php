<?php

/**
 * Message Forum Manager
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Demeter
 * @update 06.10.13
*/
namespace Asylamba\Modules\Demeter\Manager\Forum;

use Asylamba\Classes\Worker\Manager;
use Asylamba\Classes\Database\Database;
use Asylamba\Classes\Library\Utils;
use Asylamba\Modules\Demeter\Model\Forum\ForumMessage;
use Asylamba\Classes\Library\Parser;

class ForumMessageManager extends Manager
{
	protected $managerType ='_ForumMessage';

	public function __construct(
		protected Database $database,
		protected Parser $parser,
	) {
		parent::__construct($database);

		$this->parser = $parser;
	}
	
	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'm.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$qr = $this->database->prepare('SELECT m.*,
				p.name AS playerName,
				p.rColor AS playerColor,
				p.avatar AS playerAvatar,
				p.status AS playerStatus
			FROM forumMessage AS m
			LEFT JOIN player AS p
				ON m.rPlayer = p.id
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

		foreach($aw AS $awMessage) {
			$message = new ForumMessage();
			$message->id = $awMessage['id'];
			$message->rPlayer = $awMessage['rPlayer'];
			$message->rTopic = $awMessage['rTopic'];
			$message->oContent = $awMessage['oContent'];
			$message->pContent = $awMessage['pContent'];
			$message->statement = $awMessage['statement'];
			$message->dCreation = $awMessage['dCreation'];
			$message->dLastModification = $awMessage['dLastModification'];

			$message->playerName = $awMessage['playerName'];
			$message->playerColor = $awMessage['playerColor'];
			$message->playerAvatar = $awMessage['playerAvatar'];
			$message->playerStatus = $awMessage['playerStatus'];

			$this->_Add($message);
		}
	}

	public function save() {
		$messages = $this->_Save();

		foreach ($messages AS $message) {
			
			$qr = $this->database->prepare('UPDATE forumMessage
				SET
					rPlayer = ?,
					rTopic = ?,
					oContent = ?,
					pContent = ?,
					statement = ?,
					dCreation = ?,
					dLastModification = ?
				WHERE id = ?');
			$aw = $qr->execute(array(
					$message->rPlayer,
					$message->rTopic,
					$message->oContent,
					$message->pContent,
					$message->statement,
					$message->dCreation,
					Utils::now(),
					$message->id
				));
		}
	}

	public function add($newMessage) {
		$qr = $this->database->prepare('INSERT INTO forumMessage
			SET
				rPlayer = ?,
				rTopic = ?,
				oContent = ?,
				pContent = ?,
				dCreation = ?');
		$aw = $qr->execute(array(
				$newMessage->rPlayer,
				$newMessage->rTopic,
				$newMessage->oContent,
				$newMessage->pContent,
				Utils::now()
				));

		$newMessage->id = $this->database->lastInsertId();

		$this->_Add($newMessage);

		return $newMessage->id;
	}

	public function deleteById($id) {
		$qr = $this->database->prepare('DELETE FROM forumMessage WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		return TRUE;
	}

	public function edit(ForumMessage $message, $content) {
		$message->oContent = $content;

		$this->parser->parseBigTag = TRUE;

		$message->pContent = $this->parser->parse($content);
	}
}
