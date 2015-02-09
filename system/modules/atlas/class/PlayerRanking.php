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

	public $general;			# pts des bases + flottes + commandants
	public $generalPosition;
	public $generalVariation;

	public $experience;
	public $experiencePosition;
	public $experienceVariation;

	public $butcher;
	public $butcherPosition;
	public $butcherVariation;

	public $trader;
	public $traderPosition;
	public $traderVariation;

	public $fight; 				# nbr victoires - nbr défaites 
	public $fightPosition;
	public $fightVariation;

	public $armies;
	public $armiesPosition;
	public $armiesVariation;

	public $resources;
	public $resourcesPosition;
	public $resourcesVariation;

	# additional attributes
	public $color;
	public $name;
	public $avatar;
	public $status;
	

	public function getId() { return $this->id; }

	public function commonRender($type) {
		$r = '';
		$status = ColorResource::getInfo($this->color, 'status');

		switch ($type) {
			case 'general':
				$pos = $this->generalPosition;
				$var = $this->generalVariation; break;
			case 'xp':
				$pos = $this->experiencePosition;
				$var = $this->experienceVariation; break;
			case 'butcher':
				$pos = $this->butcherPosition;
				$var = $this->butcherVariation; break;
			case 'trader':
				$pos = $this->traderPosition;
				$var = $this->traderVariation; break;
			case 'fight':
				$pos = $this->fightPosition;
				$var = $this->fightVariation; break;
			default: $var = ''; $pos = ''; break;
		}

		$r .= '<div class="player color' . $this->color . ' ' . (CTR::$data->get('playerId') == $this->rPlayer ? 'active' : NULL) . '">';
			$r .= '<a href="' . APP_ROOT . 'diary/player-' . $this->rPlayer . '">';
				$r .= '<img src="' . MEDIA . 'avatar/small/' . $this->avatar . '.png" alt="' . $this->name . '" />';
			$r .= '</a>';

			$r .= '<span class="title">' . $status[$this->status - 1] . '</span>';
			$r .= '<strong class="name">' . $this->name . '</strong>';

			$r .= '<span class="experience">';
				switch ($type) {
					case 'general': $r .= Format::numberFormat($this->general) . ' point' . Format::addPlural($this->general); break;
					case 'xp': $r .= Format::numberFormat($this->experience) . ' xp'; break;
					case 'butcher': $r .= Format::numberFormat($this->butcher) . ' victoire' . Format::addPlural($this->butcher); break;
					case 'trader': $r .= Format::numberFormat($this->trader) . ' défaite' . Format::addPlural($this->trader); break;
					case 'fight': $r .= Format::numberFormat($this->fight) . ' point' . Format::addPlural($this->fight) . ' de combat'; break;
					case 'armies': $r .= Format::numberFormat($this->armies) . ' point' . Format::addPlural($this->armies) . ' de combat'; break;
					case 'resources': $r .= Format::numberFormat($this->resources) . ' point' . Format::addPlural($this->resources) . ' de combat'; break;
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
