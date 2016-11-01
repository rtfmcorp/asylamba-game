<?php

/**
 * Research
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Prométhée
 * @update 19.04.13
*/
namespace Asylamba\Modules\Promethee\Model;

use Asylamba\Modules\Promethee\Resource\ResearchResource;
use Asylamba\Classes\Container\StackList;
use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Worker\CTR;
use Asylamba\Modules\Hermes\Model\Notification;
use Asylamba\Modules\Demeter\Resource\ColorResource;

class Research {
	// ATTRIBUTES
	public $rPlayer;
	public $mathLevel = 0;						//naturalTech
	public $physLevel = 0;
	public $chemLevel = 0;
	public $bioLevel = 0;	# bio == law		//lifeTech
	public $mediLevel = 0;  # medi == comm
	public $econoLevel = 0;						//socialTech
	public $psychoLevel = 0;
	public $networkLevel = 0;					//informaticTech
	public $algoLevel = 0;
	public $statLevel = 0;
	public $naturalTech = 0;
	public $lifeTech = 0;
	public $socialTech = 0;
	public $informaticTech = 0;
	public $naturalToPay;
	public $lifeToPay;
	public $socialToPay;
	public $informaticToPay;

	const MATH = 0;
	const PHYS = 1;
	const CHEM = 2;
	const LAW = 3;
	const COMM = 4;
	const ECONO = 5;
	const PSYCHO = 6;
	const NETWORK = 7;
	const ALGO = 8;
	const STAT = 9;

	public function getId() {
		return $this->rPlayer;
	}

	public function getLevel($id) {
		if (ResearchResource::isAResearch($id)) {
			switch ($id) {
				case 0 : return $this->mathLevel; break;
				case 1 : return $this->physLevel; break;
				case 2 : return $this->chemLevel; break;
				case 3 : return $this->bioLevel; break;
				case 4 : return $this->mediLevel; break;
				case 5 : return $this->econoLevel; break;
				case 6 : return $this->psychoLevel; break;
				case 7 : return $this->networkLevel; break;
				case 8 : return $this->algoLevel; break;
				case 9 : return $this->statLevel; break;
				default : return FALSE;
			}
		}
		return FALSE;
	}

	public function getResearchList() {
		// return a stacklist of the researches
		$r = new StackList();
		for ($i = 0; $i < RSM_RESEARCHQUANTITY; $i++) {
			$r->append($this->getLevel($i));
		}
		return $r;
	}

	public function update($player, $naturalInvest, $lifeInvest, $socialInvest, $informaticInvest) {
		# prestige
		$S_PAM = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession();
		ASM::$pam->load(array('id' => $player));
		$p = ASM::$pam->get();
		$applyPrestige = FALSE;
		if ($p->rColor == ColorResource::APHERA) {
			$applyPrestige = TRUE;
		}
			
		// natural technologies
		do {
			if ($this->naturalToPay > $naturalInvest) {
				$this->naturalToPay -= $naturalInvest;
				$naturalInvest = 0;
			} else {
				$naturalInvest -= $this->naturalToPay;
				switch ($this->naturalTech) {
					case 0 :
						$this->mathLevel++;
						$levelReached = $this->mathLevel;
						break;
					case 1 :
						$this->physLevel++;
						$levelReached = $this->physLevel;
						break;
					case 2 :
						$this->chemLevel++;
						$levelReached = $this->chemLevel;
						break;
					default :
						$levelReached = 0;
						CTR::$alert->add('une erreur est survenue lors de la mise à jour des technologies');
				}

				$n = new Notification();
				$n->setRPlayer($player);
				$n->setTitle(ResearchResource::getInfo($this->naturalTech, 'name') . ' niveau ' . $levelReached);
				$n->setContent('Vos investissements dans l\'Université ont payé !<br />
					Vos chercheurs du département des <strong>Sciences Naturelles</strong> ont fait des avancées en <strong>' 
					. ResearchResource::getInfo($this->naturalTech, 'name') . '</strong>. Vous êtes actuellement au <strong>niveau ' 
					. $levelReached . '</strong> dans ce domaine. Félicitations !');
				ASM::$ntm->add($n);

				do {
					$this->naturalTech = rand(0, 2); // 0, 1 ou 2
					$tech1 = $this->mathLevel;
					$tech2 = $this->physLevel;
					$tech3 = $this->chemLevel;
					switch ($this->naturalTech) {
						case 0 : $tech1++; break;
						case 1 : $tech2++; break;
						case 2 : $tech3++; break;
						default :
						CTR::$alert->add('une erreur est survenue lors de la mise à jour des technologies');
					}
				} while (!ResearchResource::isResearchPermit($tech1, $tech2, $tech3));
				$this->naturalToPay = ResearchResource::getInfo($this->naturalTech, 'level', $this->getLevel($this->naturalTech) + 1, 'price');
			}
		} while ($naturalInvest > 0);
		// life technologies (en fait ce sont les sciences politiques)
		do {
			if ($this->lifeToPay > $lifeInvest) {
				$this->lifeToPay -= $lifeInvest;
				$lifeInvest = 0;
			} else {
				$lifeInvest -= $this->lifeToPay;
				switch ($this->lifeTech) {
					case 3 :
						$this->bioLevel++;
						$levelReached = $this->bioLevel;
						break;
					case 4 :
						$this->mediLevel++;
						$levelReached = $this->mediLevel;
						break;
					default :
						$levelReached = 0;
						CTR::$alert->add('une erreur est survenue lors de la mise à jour des technologies');
				}

				$n = new Notification();
				$n->setRPlayer($player);
				$n->setTitle(ResearchResource::getInfo($this->lifeTech, 'name') . ' niveau ' . $levelReached);
				$n->setContent('Vos investissements dans l\'Université ont payé !<br />
					Vos chercheurs du département des <strong>Sciences Politiques</strong> ont fait des avancées en <strong>' 
					. ResearchResource::getInfo($this->lifeTech, 'name') . '</strong>. Vous êtes actuellement au <strong>niveau ' 
					. $levelReached . '</strong> dans ce domaine. Félicitations !');
				ASM::$ntm->add($n);

				do {
					$this->lifeTech = rand(3, 4);
					$tech1 = $this->bioLevel;
					$tech2 = $this->mediLevel;
					switch ($this->lifeTech) {
						case 3 : $tech1++; break;
						case 4 : $tech2++; break;
						default :
						CTR::$alert->add('une erreur est survenue lors de la mise à jour des technologies');
					}
				} while (!ResearchResource::isResearchPermit($tech1, $tech2));
				$this->lifeToPay = ResearchResource::getInfo($this->lifeTech, 'level', $this->getLevel($this->lifeTech) + 1, 'price');
			}
		} while ($lifeInvest > 0);
		// social technologies
		do {
			if ($this->socialToPay > $socialInvest) {
				$this->socialToPay -= $socialInvest;
				$socialInvest = 0;
			} else {
				$socialInvest -= $this->socialToPay;
				switch ($this->socialTech) {
					case 5 :
						$this->econoLevel++;
						$levelReached = $this->econoLevel;
						break;
					case 6 :
						$this->psychoLevel++;
						$levelReached = $this->psychoLevel;
						break;
					default :
						$levelReached = 0;
						CTR::$alert->add('une erreur est survenue lors de la mise à jour des technologies');
				}

				$n = new Notification();
				$n->setRPlayer($player);
				$n->setTitle(ResearchResource::getInfo($this->socialTech, 'name') . ' niveau ' . $levelReached);
				$n->setContent('Vos investissements dans l\'Université ont payé !<br />
					Vos chercheurs du département des <strong>Sciences Economiques et Sociales</strong> ont fait des avancées en <strong>' 
					. ResearchResource::getInfo($this->socialTech, 'name') . '</strong>. Vous êtes actuellement au <strong>niveau ' 
					. $levelReached . '</strong> dans ce domaine. Félicitations !');
				ASM::$ntm->add($n);
				do {
					$this->socialTech = rand(5, 6);
					$tech1 = $this->econoLevel;
					$tech2 = $this->psychoLevel;
					switch ($this->socialTech) {
						case 5 : $tech1++; break;
						case 6 : $tech2++; break;
						default :
						CTR::$alert->add('une erreur est survenue lors de la mise à jour des technologies');
					}
				} while (!ResearchResource::isResearchPermit($tech1, $tech2));
				$this->socialToPay = ResearchResource::getInfo($this->socialTech, 'level', $this->getLevel($this->socialTech) + 1, 'price');
			}
		} while ($socialInvest > 0);
		// informatic technologies
		do {
			if ($this->informaticToPay > $informaticInvest) {
				$this->informaticToPay -= $informaticInvest;
				$informaticInvest = 0;
			} else {
				$informaticInvest -= $this->informaticToPay;
				switch ($this->informaticTech) {
					case 7 :
						$this->networkLevel++;
						$levelReached = $this->networkLevel;
						break;
					case 8 :
						$this->algoLevel++;
						$levelReached = $this->algoLevel;
						break;
					case 9 :
						$this->statLevel++;
						$levelReached = $this->statLevel;
						break;
					default :
						$levelReached = 0;
						CTR::$alert->add('une erreur est survenue lors de la mise à jour des technologies');
				}
				
				$n = new Notification();
				$n->setRPlayer($player);
				$n->setTitle(ResearchResource::getInfo($this->informaticTech, 'name') . ' niveau ' . $levelReached);
				$n->setContent('Vos investissements dans l\'Université ont payé !<br />
					Vos chercheurs du département de l\'<strong>Ingénierie Informatique</strong> ont fait des avancées en <strong>' 
					. ResearchResource::getInfo($this->informaticTech, 'name') . '</strong>. Vous êtes actuellement au <strong>niveau ' 
					. $levelReached . '</strong> dans ce domaine. Félicitations !');
				ASM::$ntm->add($n);

				do {
					$this->informaticTech = rand(7, 9);
					$tech1 = $this->networkLevel;
					$tech2 = $this->algoLevel;
					$tech3 = $this->statLevel;
					switch ($this->informaticTech) {
						case 7 : $tech1++; break;
						case 8 : $tech2++; break;
						case 9 : $tech3++; break;
						default :
						CTR::$alert->add('une erreur est survenue lors de la mise à jour des technologies');
					}
				} while (!ResearchResource::isResearchPermit($tech1, $tech2, $tech3));
				$this->informaticToPay = ResearchResource::getInfo($this->informaticTech, 'level', $this->getLevel($this->informaticTech) + 1, 'price');
			}
		} while ($informaticInvest > 0);
		ASM::$pam->changeSession($S_PAM);
	}
}