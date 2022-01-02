<?php

/**
 * Topic Forum Manager
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
use Asylamba\Modules\Demeter\Model\Forum\ForumTopic;

class ForumTopicManager extends Manager
{
	protected $managerType ='_ForumTopic';

	public function __construct(Database $database)
	{
		parent::__construct($database);
	}
	
	public function load($where = array(), $order = array(), $limit = array(), $playerId = FALSE) {
		$formatWhere = Utils::arrayToWhere($where, 't.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$qr = $this->database->prepare('SELECT t.*,
				(SELECT lv.dView FROM forumLastView AS lv WHERE rTopic = t.id AND rPlayer = ?) AS lastView,
				COUNT(m.id) AS nbMessage
			FROM forumTopic AS t
			LEFT JOIN forumMessage AS m
				ON t.id = m.rTopic
			' . $formatWhere . '
			GROUP BY t.id
			' . $formatOrder . '
			' . $formatLimit
		);

		$valuesArray[] = $playerId;
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

		foreach ($aw AS $awTopic) {
			$topic = new ForumTopic();
			$topic->id = $awTopic['id'];
			$topic->title = $awTopic['title'];
			$topic->rPlayer = $awTopic['rPlayer'];
			$topic->rColor = $awTopic['rColor'];
			$topic->rForum = $awTopic['rForum'];
			$topic->isArchived = $awTopic['isArchived'];
			$topic->isUp = $awTopic['isUp'];
			$topic->isClosed = $awTopic['isClosed'];
			$topic->dCreation = $awTopic['dCreation'];
			$topic->dLastMessage = $awTopic['dLastMessage'];
			
			$topic->lastView = $awTopic['lastView'];
			$topic->nbMessage = $awTopic['nbMessage'];

			$this->_Add($topic);
		}		
	}

	public function save() {
		$topics = $this->_Save();

		foreach ($topics AS $topic) {
			$qr = $this->database->prepare('UPDATE forumTopic
			SET
				title = ?,
				rPlayer = ?,
				rColor = ?,
				rForum = ?,
				isArchived = ?,
				isUp = ?,
				isClosed = ?,
				dCreation = ?,
				dLastMessage = ?
			WHERE id = ?');
			$aw = $qr->execute(array(
				$topic->title,
				$topic->rPlayer,
				$topic->rColor,
				$topic->rForum,
				$topic->isArchived,
				$topic->isUp,
				$topic->isClosed,
				$topic->dCreation,
				$topic->dLastMessage,
				$topic->id
			));
		}
	}

	public function add($newTopic) {
		$qr = $this->database->prepare('INSERT INTO forumTopic
			SET
				title = ?,
				rPlayer = ?,
				rColor = ?,
				rForum = ?,
				isArchived = ?,
				isUp = ?,
				isClosed = ?,
				dCreation = ?,
				dLastMessage = ?');
		$aw = $qr->execute(array(
				$newTopic->title,
				$newTopic->rPlayer,
				$newTopic->rColor,
				$newTopic->rForum,
				$newTopic->isArchived,
				$newTopic->isUp,
				$newTopic->isClosed,
				Utils::now(),
				Utils::now()
				)
		);

		$newTopic->id = $this->database->lastInsertId();

		$this->_Add($newTopic);

		return $newTopic->id;
	}

	public function deleteById($id) {
		$qr = $this->database->prepare('DELETE FROM forumTopic WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		return TRUE;
	}

	public function updateLastView(ForumTopic $topic, $playerId) {
		if ($topic->lastView == NULL) {
			$qr = $this->database->prepare('INSERT INTO forumLastView 
				SET
					rPlayer = ?,
					rTopic = ?,
					dView = ?');
			$aw = $qr->execute(array(
					$playerId,
					$topic->id,
					Utils::now()
				)
			);
		} else {
			$qr = $this->database->prepare('UPDATE forumLastView
				SET
					dView = ?
				WHERE rPlayer = ? AND rTopic = ?');
			$aw = $qr->execute(array(
				Utils::now(),
				$playerId,
				$topic->id
			));
		}
	}
}
