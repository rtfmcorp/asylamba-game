<?php

/**
 * Sector Manager
 *
 * @author Expansion
 * @copyright Expansion - le jeu
 *
 * @package Gaia
 * @update 20.05.13
*/
namespace Asylamba\Modules\Gaia\Manager;

use Asylamba\Classes\Library\Utils;

use Asylamba\Classes\Entity\EntityManager;

use Asylamba\Modules\Gaia\Model\Sector;

class SectorManager {
	/** @var EntityManager **/
	protected $entityManager;
	
	/**
	 * @param EntityManager $entityManager
	 */
	public function __construct(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
	}

	/**
	 * @param int $id
	 * @return Sector
	 */
	public function get($id) {
		return $this->entityManager->getRepository(Sector::class)->get($id);
	}
	
	public function getFactionSectors($factionId)
	{
		return $this->entityManager->getRepository(Sector::class)->getFactionSectors($factionId);
	}
	
	public function getAll()
	{
		return $this->entityManager->getRepository(Sector::class)->getAll();
	}

	public function loadByOr($module, $list = array()) {
		$query = '';
		foreach ($list as $v) { $query .= $module . ' = ? OR '; }
		$query = trim($query, 'OR ');

		$qr = $this->database->prepare('SELECT 
			DISTINCT(se.id),
			se.*
			FROM sector AS se
			LEFT JOIN system AS sy
				ON sy.rSector = se.id
			LEFT JOIN place AS pl
				ON pl.rSystem = sy.id
			WHERE ' . $query);

		if (empty($list)) {
			$qr->execute();
		} else {
			$qr->execute($list);
		}

		$aw = $qr->fetchAll();
		if (!empty($aw)) {
			foreach ($aw as $s) {
				$sector = new Sector();

				$sector->setId($s['id']);
				$sector->setRColor($s['rColor']);
				$sector->rSurrender = $s['rSurrender'];
				$sector->setXPosition($s['xPosition']);
				$sector->setYPosition($s['yPosition']);
				$sector->setXBarycentric($aw['xBarycentric']);
				$sector->setYBarycentric($aw['yBarycentric']);
				$sector->setTax($s['tax']);
				$sector->setName($s['name']);
				$sector->setPoints($s['points']);
				$sector->setPopulation($s['population']);
				$sector->setLifePlanet($s['lifePlanet']);

				$this->sectors[] = array($sector, TRUE);
			}
			return TRUE;
		} else {
			return FALSE;
		}
	}
}