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
namespace App\Modules\Athena\Manager;

use App\Classes\Worker\Manager;
use App\Classes\Library\Utils;
use App\Classes\Database\Database;

use App\Modules\Athena\Model\CommercialTax;
use App\Modules\Demeter\Model\Color;

class CommercialTaxManager extends Manager
{
	protected $managerType = '_CommercialTax';

	public function __construct(Database $database) {
		parent::__construct($database);
	}

	public function getFactionsTax(Color $faction, Color $relatedFaction): ?CommercialTax
	{
		$statement = $this->database->prepare('SELECT * FROM commercialTax WHERE faction = :faction_id AND relatedFaction = :related_faction_id');
		$statement->execute([
			'faction_id' => $faction->getId(),
			'related_faction_id' => $relatedFaction->getId(),
		]);

		return (false !== ($result = $statement->fetch())) ? $this->format($result) : null;
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

		while($data = $qr->fetch()) {
			$currentT = $this->_Add($this->format($data));
		}
	}
	
	protected function format(array $data): CommercialTax
	{
		$ct = new CommercialTax();

		$ct->id = $data['id'];
		$ct->faction = $data['faction'];
		$ct->relatedFaction = $data['relatedFaction'];
		$ct->exportTax = $data['exportTax'];
		$ct->importTax = $data['importTax'];
		
		return $ct;
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
