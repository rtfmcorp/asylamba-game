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
namespace Asylamba\Modules\Demeter\Manager\Forum;

use Asylamba\Classes\Worker\Manager;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Database\Database;
use Asylamba\Modules\Demeter\Model\Forum\FactionNews;

class FactionNewsManager extends Manager {
	protected $managerType ='_factionNews';

	public function __construct(Database $database) {
		parent::__construct($database);
	}
	
	public function load($where = array(), $order = array(), $limit = array()) {
		$formatWhere = Utils::arrayToWhere($where, 'n.');
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$qr = $this->database->prepare('SELECT n.* 
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
			$news = new FactionNews();
			$news->id = $awNews['id'];
			$news->rFaction = $awNews['rFaction'];
			$news->title = $awNews['title'];
			$news->oContent = $awNews['oContent'];
			$news->pContent = $awNews['pContent'];
			$news->pinned = $awNews['pinned'];
			$news->statement = $awNews['statement'];
			$news->dCreation = $awNews['dCreation'];

			$this->_Add($news);
		}
	}

	public function save() {
		$newsArray = $this->_Save();

		foreach ($newsArray AS $news) {
			
			$qr = 'UPDATE factionNews
				SET
					rFaction = ?,
					title = ?,
					oContent = ?,
					pContent = ?,
					pinned = ?,
					statement = ?,
					dCreation = ?
				WHERE id = ?';

			$qr = $this->database->prepare($qr);
			
			$aw = $qr->execute(array(
					$news->rFaction,
					$news->title,
					$news->oContent,
					$news->pContent,
					$news->pinned,
					$news->statement,
					$news->dCreation,
					$news->id
				));
		}
	}

	public function add($news) {
		$qr = $this->database->prepare('INSERT INTO factionNews
			SET
				rFaction = ?,
				title = ?,
				oContent = ?,
				pContent = ?,
				pinned = ?,
				statement = ?,
				dCreation = ?');
		$aw = $qr->execute(array(
				$news->rFaction,
				$news->title,
				$news->oContent,
				$news->pContent,
				$news->pinned,
				$news->statement,
				Utils::now()
				));

		$news->id = $this->database->lastInsertId();

		$this->_Add($news);

		return $news->id;
	}

	public function deleteById($id) {
		$qr = $this->database->prepare('DELETE FROM factionNews WHERE id = ?');
		$qr->execute(array($id));

		$this->_Remove($id);
		return TRUE;
	}
}
