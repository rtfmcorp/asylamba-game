<?php

/**
 * CommercialTaxManager
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Athena
 * @version 05.03.14
 **/
namespace Asylamba\Modules\Athena\Manager;

use Asylamba\Classes\Worker\Manager;
use Asylamba\Classes\Library\Utils;
use Asylamba\Classes\Database\Database;

use Asylamba\Modules\Athena\Model\CommercialTax;

class CommercialTaxManager extends Manager
{
	protected $managerType = '_CommercialTax';

	public function __construct(Database $database) {
		parent::__construct($database);
	}
	
	public function load($where = array(), $order = array(), $limit = array())
	{
		$formatWhere = Utils::arrayToWhere($where);
		$formatOrder = Utils::arrayToOrder($order);
		$formatLimit = Utils::arrayToLimit($limit);

		$qr = $this->database->prepare('SELECT *
			FROM commercialTax
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
			$ct = new CommercialTax();

			$ct->id = $aw['id'];
			$ct->faction = $aw['faction'];
			$ct->relatedFaction = $aw['relatedFaction'];
			$ct->exportTax = $aw['exportTax'];
			$ct->importTax = $aw['importTax'];
			
			$currentT = $this->_Add($ct);
		}
	}

	public function add(CommercialTax $ct) {
		$qr = $this->database->prepare('INSERT INTO
			commercialTax(faction, relatedFaction, exportTax, importTax)
			VALUES(?, ?, ?, ?)');
		$qr->execute(array(
			$ct->faction,
			$ct->relatedFaction,
			$ct->exportTax,
			$ct->importTax
		));

		$ct->id = $this->database->lastInsertId();

		$this->_Add($ct);
	}

	public function save() {
		$commercialTaxes = $this->_Save();

		foreach ($commercialTaxes AS $t) {
			$qr = $this->database->prepare('UPDATE commercialTax
				SET	id = ?,
					faction = ?,
					relatedFaction = ?,
					exportTax = ?,
					importTax = ?
				WHERE id = ?');
			$qr->execute(array(
				$t->id,
				$t->faction,
				$t->relatedFaction,
				$t->exportTax,
				$t->importTax,
				$t->id
			));
		}
	}
}
