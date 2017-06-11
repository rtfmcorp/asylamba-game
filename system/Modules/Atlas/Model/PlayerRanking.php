<?php

/**
 * PlayerRanking
 *
 * @author Jacky Casas
 * @copyright Asylamba
 *
 * @package Atlas
 * @update 04.06.14
 */

namespace Asylamba\Modules\Atlas\Model;

use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Demeter\Resource\ColorResource;

use Asylamba\Modules\Zeus\Model\Player;

class PlayerRanking {
	# set number of player before you (remove 1) in rank view
	const PREV = 4;
	# set number of player after you in rank view
	const NEXT = 8;
	# PREV + NEXT
	const STEP = 12;
	# set number of player on ajax load page
	const PAGE = 10;
	
	# attributes
	public $id; 
	public $rRanking;
	public $rPlayer; 
	
	/** @var Player **/
	protected $player;

	public $general;			# pts des bases + flottes + commandants
	public $generalPosition;
	public $generalVariation;

	public $experience; 		# experience
	public $experiencePosition;
	public $experienceVariation;

	public $butcher;			# destroyedPEV - lostPEV
	public $butcherDestroyedPEV;
	public $butcherLostPEV;
	public $butcherPosition;
	public $butcherVariation;

	public $trader;				# revenu total des routes
	public $traderPosition;
	public $traderVariation;

	public $fight; 				# victoires - défaites 
	public $victories;
	public $defeat;
	public $fightPosition;
	public $fightVariation;

	public $armies;				# nb de pev total flotte + hangar
	public $armiesPosition;
	public $armiesVariation;

	public $resources; 			# production de ressources par relève (on peut ajouter les recyclages p-e)
	public $resourcesPosition;
	public $resourcesVariation;

	# additional attributes
	public $color;
	public $name;
	public $avatar;
	public $status;
	
	/**
	 * @param int $id
	 * @return PlayerRanking
	 */
	public function setId($id)
	{
		$this->id = $id;
		
		return $this;
	}

	public function getId() { return $this->id; }

	/**
	 * @param Player $player
	 * @return PlayerRanking
	 */
	public function setPlayer(Player $player)
	{
		$this->player = $player;
		
		return $this;
	}
	
	/**
	 * @return Player
	 */
	public function getPlayer()
	{
		return $this->player;
	}
	
	/**
	 * @param int $general
	 * @return PlayerRanking
	 */
	public function setGeneral($general)
	{
		$this->general = $general;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getGeneral()
	{
		return $this->general;
	}
	
	public function commonRender($playerId, $type) {
		$r = '';
		$status = ColorResource::getInfo($this->color, 'status');

		$plus = null;
		$minus = null;

		switch ($type) {
			case 'general':
				$pos = $this->generalPosition;
				$var = $this->generalVariation; break;
			case 'xp':
				$pos = $this->experiencePosition;
				$var = $this->experienceVariation; break;
			case 'butcher':
				$pos = $this->butcherPosition;
				$plus = $this->butcherDestroyedPEV;
				$minus = $this->butcherLostPEV;
				$var = $this->butcherVariation; break;
			case 'trader':
				$pos = $this->traderPosition;
				$var = $this->traderVariation; break;
			case 'fight':
				$pos = $this->fightPosition;
				$plus = $this->victories;
				$minus = $this->defeat;
				$var = $this->fightVariation; break;
			case 'armies':
				$pos = $this->armiesPosition;
				$var = $this->armiesVariation; break;
			case 'resources':
				$pos = $this->resourcesPosition;
				$var = $this->resourcesVariation; break;
			default: $var = ''; $pos = ''; break;
		}

		$r .= '<div class="player color' . $this->color . ' ' . ($playerId == $this->rPlayer ? 'active' : NULL) . '">';
			$r .= '<a href="' . APP_ROOT . 'embassy/player-' . $this->rPlayer . '">';
				$r .= '<img src="' . MEDIA . 'avatar/small/' . $this->avatar . '.png" alt="' . $this->name . '" class="picto" />';
			$r .= '</a>';

			$r .= '<span class="title">' . $status[$this->status - 1] . '</span>';
			$r .= '<strong class="name">' . $this->name . '</strong>';

			$r .= '<span class="experience">';
				switch ($type) {
					case 'general': $r .= Format::numberFormat($this->general) . ' point' . Format::addPlural($this->general); break;
					case 'xp': $r .= Format::numberFormat($this->experience) . ' xp'; break;
					case 'butcher': 
						$r .= Format::numberFormat($this->butcher) . ' point' . Format::addPlural($this->butcher);
						break;
					case 'trader': $r .= Format::numberFormat($this->trader) . ' <img src="' . MEDIA . 'resources/credit.png" class="icon-color" alt="crédits" />'; break;
					case 'fight': 
						$r .= Format::numberFormat($this->fight) . ' point' . Format::addPlural($this->fight);
						break;
				#	case 'armies': $r .= Format::numberFormat($this->armies) . ' <img src="' . MEDIA . 'resources/pev.png" class="icon-color" alt="pev" />'; break;
					case 'resources': $r .= Format::numberFormat($this->resources) . ' <img src="' . MEDIA . 'resources/resource.png" class="icon-color" alt="ressources" />'; break;
					default: break;
				}
			$r .= '</span>';

			$r .= '<span class="position">' . $pos . '</span>';

			if (intval($var) != 0) {
				$r .= '<span class="variance ' . ($var > 0 ? ' upper' : ' lower') . '">';
					$r .= ($var > 0 ? '+' : '–') . abs($var);
				$r .='</span>';
			}
		$r .= '</div>';

		return $r;
	}
}
