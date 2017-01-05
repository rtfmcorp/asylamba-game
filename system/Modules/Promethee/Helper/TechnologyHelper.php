<?php

namespace Asylamba\Modules\Promethee\Helper;

use Asylamba\Classes\Container\StackList;
use Asylamba\Classes\Container\ArrayList;
use Asylamba\Classes\Exception\ErrorException;

use Asylamba\Modules\Athena\Model\OrbitalBase;
use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;
use Asylamba\Modules\Athena\Helper\OrbitalBaseHelper;
use Asylamba\Modules\Promethee\Model\Technology;
use Asylamba\Modules\Promethee\Resource\TechnologyResource;

class TechnologyHelper {
	/** @var OrbitalBaseHelper **/
	protected $orbitalBaseHelper;
	/** @var ResearchHelper **/
	protected $researchHelper;
	/** @var int **/
	protected $researchQuantity;
	
	/**
	 * @param OrbitalBaseHelper $orbitalBaseHelper
	 * @param ResearchHelper $researchHelper
	 * @param int $researchQuantity
	 */
	public function __construct(OrbitalBaseHelper $orbitalBaseHelper, ResearchHelper $researchHelper, $researchQuantity)
	{
		$this->orbitalBaseHelper = $orbitalBaseHelper;
		$this->researchHelper = $researchHelper;
		$this->researchQuantity = $researchQuantity;
	}
	
	public function isATechnology($techno) {
		return in_array($techno, TechnologyResource::$technologies);
	}

	public function isAnUnblockingTechnology($techno) {
		return in_array($techno, TechnologyResource::$technologiesForUnblocking);
	}

	public function isATechnologyNotDisplayed($techno) {
		return in_array($techno, TechnologyResource::$technologiesNotDisplayed);
	}

	public function getInfo($techno, $info, $level = 0) {
		if ($this->isATechnology($techno)) {
			if ($this->isAnUnblockingTechnology($techno)) {
				if(in_array($info, array('name', 'progName', 'imageLink', 'requiredTechnosphere', 'requiredResearch', 'time', 'resource', 'credit', 'points', 'column', 'shortDescription', 'description'))) {
					return TechnologyResource::$technology[$techno][$info];
				} else {
					throw new ErrorException('2e argument faux pour getInfo() de TechnologyResource (techno ' . $techno . ', ' . $info . ')');
				}
			} else {
				if(in_array($info, array('name', 'progName', 'imageLink', 'requiredTechnosphere', 'requiredResearch', 'maxLevel', 'category', 'column', 'shortDescription', 'description', 'bonus'))) {
					return TechnologyResource::$technology[$techno][$info];
				} elseif (in_array($info, array('time', 'resource', 'credit', 'points'))) {
					if ($level <= 0) {
						return FALSE;
					}
					if ($info == 'points') {
						return round(TechnologyResource::$technology[$techno][$info] * $level * Technology::COEF_POINTS);
					} elseif ($info == 'time') {
						return round(TechnologyResource::$technology[$techno][$info] * $level * Technology::COEF_TIME);
					} else {

						switch (TechnologyResource::$technology[$techno]['category']) {
							case 1:
								$value = round(TechnologyResource::$technology[$techno][$info] * pow(1.5, $level-1));
								break;
							case 2:
								$value = round(TechnologyResource::$technology[$techno][$info] * pow(1.3, $level-1));
								break;
							case 3:
								$value = round(TechnologyResource::$technology[$techno][$info] * pow(1.2, $level-1));
								break;
							default:
								return FALSE;
						}

					#	$value = round($this->technology[$techno][$info] * pow(1.75, $level-1));
					#	$value = round($this->technology[$techno][$info] * pow(1.5, $level-1));
					#	$value = round($this->technology[$techno][$info] * pow(1.3, $level-1));

						return $value;
					}
				} else {
					throw new ErrorException('2e argument faux pour getInof() de TechnologyResource');
				}
			}
		} else {
			throw new ErrorException('Technologie inexistante dans getInfo() de TechnologyResource ' . $techno);
		}
		return FALSE;
	}

	public function haveRights($techno, $type, $arg1 = 0, $arg2 = 'default') {
		if ($this->isATechnology($techno)) {
			switch($type) {
				// assez de ressources pour contruire ?
				// $arg1 est le niveau
				// $arg2 est ce que le joueur possède (ressource ou crédit)
				case 'resource' : return ($arg2 >= $this->getInfo($techno, 'resource', $arg1)) ? TRUE : FALSE;
					break;
				// assez de crédits pour construire ?
				case 'credit' : return ($arg2 >= $this->getInfo($techno, 'credit', $arg1)) ? TRUE : FALSE;
					break;
				// encore de la place dans la queue ?
				// $arg1 est un objet de type OrbitalBase
				// $arg2 est le nombre de technologies dans la queue
				case 'queue' : 
					$maxQueue = $this->orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::TECHNOSPHERE, 'level', $arg1->levelTechnosphere, 'nbQueues');
					return ($arg2 < $maxQueue) ? TRUE : FALSE;
					break;
				// a-t-on le droit de construire ce niveau ?
				// $arg1 est le niveau cible
				case 'levelPermit' :
				 	if ($this->isAnUnblockingTechnology($techno)) {
				 		return ($arg1 == 1) ? TRUE : FALSE;
				 	} else {
				 		//limitation de niveau ?
				 		if ($arg1 > 0) {
				 			return TRUE;
				 		} else {
				 			return FALSE;
				 		}
				 	}
				// est-ce que le niveau de la technosphère est assez élevé ?
				// arg1 est le niveau de la technosphere
				case 'technosphereLevel' :
					return ($this->getInfo($techno, 'requiredTechnosphere') <= $arg1) ? TRUE : FALSE;
					break;
				// est-ce que les recherches de l'université sont acquises ?
				// arg1 est le niveau de la technologie
				// arg2 est une stacklist avec les niveaux de recherche
				case 'research' :
					$neededResearch = $this->getInfo($techno, 'requiredResearch');
					$researchList = new StackList();
					for ($i = 0; $i < $this->researchQuantity; $i++) {
						if ($neededResearch[$i] > 0) {
							if ($arg2->get($i) < ($neededResearch[$i] + $arg1 - 1)) {
								$r = new ArrayList();
								$r->add('techno', $this->researchHelper->getInfo($i, 'name'));
								$r->add('level', $neededResearch[$i] + $arg1 - 1);
								$researchList->append($r);
							}
						}
					}
					if ($researchList->size() > 0) {
						return $researchList;
					} else {
						return TRUE;
					}
					break;
				// est-ce qu'on peut construire la techno ? Pas dépassé le niveau max
				// arg1 est le niveau de la technologie voulue
				case 'maxLevel' :
					if ($this->isAnUnblockingTechnology($techno)) {
						return TRUE;
					} else {
						return ($arg1 <= $this->getInfo($techno, 'maxLevel')) ? TRUE : FALSE;
					}
					break;
				// est-ce qu'on peut construire la techno en fonction du type de la base ?
				// arg1 est le type de la base
				case 'baseType' :
					switch ($arg1) {
						case OrbitalBase::TYP_NEUTRAL:
							return in_array($this->getInfo($techno, 'column'), array(1, 2, 3));
							break;
						case OrbitalBase::TYP_COMMERCIAL:
							return in_array($this->getInfo($techno, 'column'), array(1, 2, 3, 4, 5));
							break;
						case OrbitalBase::TYP_MILITARY:
							return in_array($this->getInfo($techno, 'column'), array(1, 2, 3, 6, 7));
							break;
						case OrbitalBase::TYP_CAPITAL:
							return in_array($this->getInfo($techno, 'column'), array(1, 2, 3, 4, 5, 6, 7));
							break;
						default:
							return FALSE;
							break;
					}
					break;
				default :
					throw new ErrorException('Erreur dans haveRights() de TechnologyResource');
			}
		} else {
			throw new ErrorException('Technologie inexistante dans haveRights() de TechnologyResource');
		}
	}

	public function getImprovementPercentage($techno, $level = -1) {
		if (!$this->isAnUnblockingTechnology($techno)) {
			$baseBonus = $this->getInfo($techno, 'bonus');
			if ($level == 0) {
				return 0;
			} elseif ($level == -1) {
				return $baseBonus;
			} else {
				return $baseBonus + floor(($level - 1) / 5);
			}
		}
		return 0;
	}
}