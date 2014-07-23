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
	const PREV = 3;
	# set number of player after you in rank view
	const NEXT = 2;
	# PREV + NEXT
	const STEP = 5;
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

	public $victory;
	public $victoryPosition;
	public $victoryVariation;

	public $defeat;
	public $defeatPosition;
	public $defeatVariation;

	public $ratio; 				# ratio victory - defeat 
	public $ratioPosition;
	public $ratioVariation;

	# additional attributes
	public $color;
	public $name;
	# public $...
	

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
			case 'victory':
				$pos = $this->victoryPosition;
				$var = $this->victoryVariation; break;
			case 'defeat':
				$pos = $this->defeatPosition;
				$var = $this->defeatVariation; break;
			case 'ratio':
				$pos = $this->ratioPosition;
				$var = $this->ratioVariation; break;
			default: $var = ''; $pos = ''; break;
		}

		$r .= '<div class="player color' . $this->color . ' ' . (CTR::$data->get('playerId') == $this->rPlayer ? 'active' : NULL) . '">';
			$r .= '<a href="' . APP_ROOT . 'diary/player-' . $this->rPlayer . '">';
				$r .= '<img src="' . MEDIA . 'avatar/small/0' . rand(10, 50) . '-' . $this->color . '.png" alt="' . $this->name . '" />';
			$r .= '</a>';

		#	$r .= '<span class="title">' . $status[$this->getStatus() - 1] . '</span>';
			$r .= '<span class="title">' . $status[rand(0, 3)] . '</span>';
			$r .= '<strong class="name">' . $this->name . '</strong>';

			$r .= '<span class="experience">';
				switch ($type) {
					case 'general': $r .= Format::numberFormat($this->general) . ' points'; break;
					case 'xp': $r .= Format::numberFormat($this->experience) . ' xp'; break;
					case 'victory': $r .= Format::numberFormat($this->victory) . ' victoire' . Format::addPlural($this->victory); break;
					case 'defeat': $r .= Format::numberFormat($this->defeat) . ' défaite' . Format::addPlural($this->defeat); break;
					case 'ratio': $r .= Format::numberFormat($this->ratio) . ' V/D'; break;
					default: break;
				}
			$r .= '</span>';

			$r .= '<span class="position';
				$r .= intval($var) == 0
					? NULL
					: ($var > 0
						? ' upper'
						: ' lower'
					)
				;
			$r .= '">' . $pos . '</span>';
		$r .= '</div>';

		return $r;
	}
}
