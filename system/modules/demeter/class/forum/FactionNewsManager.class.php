<?php

/**
 * news de faction
 *
 * @author Noé Zufferey
 * @copyright Asylamba
 *
 * @package Demeter
 * @update 09.01.15
*/

class FactionNewsManager extends Manager {
	protected $managerType ='_factionNews';

	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'n.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT n.* 
			FROM factionNews AS n
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

		foreach($aw AS $awNews) {
			$news = new factionNews();
			$news->id = $awNews['id'];
			$news->rColor = $awNews['rColor'];
			$news->title = $awNews['title'];
			$news->content = $awNews['content'];
			$news->pinned = $awNews['pinned'];
			$news->statement = $awNews['statement'];
			$news->dCreation = $awNews['dCreation'];

			$this->_Add($news);
		}
	}

	public function save() {
		$db = DataBase::getInstance();

		$newsArray = $this->_Save();

		foreach ($newsArray AS $news) {
			
			$qr = $db->prepare('UPDATE factionNews
				SET
					rColor = ?,
					title = ?,
					content = ?,
					pinned = ?,
					statement = ?,
					dCreation = ?,
				WHERE id = ?');
			$aw = $qr->execute(array(
					$news->rColor,
					$news->title,
					$news->content,
					$news->pinned,
					$news->statement,
					$news->dCreation,
					$news->id
				));
		}
	}

	public function add($news) {
		$db = DataBase::getInstance();

		$qr = $db->prepare('INSERT INTO factionNews
			SET
				rColor = ?,
				title = ?,
				content = ?,
				pinned = ?,
				statement = ?,
				dCreation = ?');
		$aw = $qr->execute(array(
				$news->rColor,
				$news->title,
				$news->content,
				$news->pinned,
				$news->statement,
				Utils::now()
				));

		$news->id = $db->lastInsertId();

		$this->_Add($news);

		return $news->id;
	}

	public function deleteById($id) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('DELETE FROM factionNews WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		return TRUE;
	}
}
