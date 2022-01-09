<?php

/**
 * CommercialTax
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Athena
 * @update 05.03.14
 */
namespace App\Modules\Athena\Model;

class CommercialTax {

	public $id = 0;
	public $faction = 0;
	public $relatedFaction = 0;
	public $exportTax;
	public $importTax;

	public function getId() { return $this->id; }
	public function getFaction() { return $this->faction; }
	public function getRelatedFaction() { return $this->relatedFaction; }
	public function getExportTax() { return $this->exportTax; }
	public function getImportTax() { return $this->importTax; }

	public function setId( $id)
	{
		$this->id = $id;
		return $this;
	}
	public function setFaction( $faction)
	{
		$this->faction = $faction;
		return $this;
	}
	public function setRelatedFaction( $relatedFaction)
	{
		$this->relatedFaction = $relatedFaction;
		return $this;
	}
	public function setExportTax( $exportTax)
	{
		$this->exportTax = $exportTax;
		return $this;
	}
	public function setImportTax( $importTax)
	{
		$this->importTax = $importTax;
		return $this;
	}
}
