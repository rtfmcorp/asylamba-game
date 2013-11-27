<?php

/**
 * Colorm Manager
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Demeter
 * @update 26.11.13
*/

class ColorManager extends Manager {
	protected $managerType ='_Color';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'm.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT c.*,
			FROM color AS c
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

		foreach($aw AS $awColor) {
			$color = new Color();

			$color->$id = $awColors['id'];
			$color->$alive = $awColors['alive'];
			$color->$credits = $awColors['credits'];
			$color->$players = $awColors['players'];
			$color->$activePlayers = $awColors['activePlayers'];
			$color->$points = $awColors['points'];
			$color->$sectors = $awColors['sectors'];
			$color->$electionStatement = $awColors['electionStatement'];
			$color->$dLastElection = $awColors['dLastElection'];

			$this->_Add($color);
		}
	}

	public function save() {
		$db = DataBase::getInstance();

		$messages = $this->_Save();

	foreach ($messages AS $message) {


		$qr = $db->prepare('UPDATE forumMessage
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
		$db = DataBase::getInstance();

		$qr = $db->prepare('INSERT INTO forumMessage
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

		$newMessage->id = $db->lastInsertId();

		$this->_Add($newMessage);

		return $newMessage->id;
	}

	public function deleteById($id) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('DELETE FROM forumMessage WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		return TRUE;
	}
}
