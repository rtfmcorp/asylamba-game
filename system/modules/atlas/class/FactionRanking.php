<?php

/**
 * FactionRanking
 *
 * @author Jacky Casas
 * @copyright Asylamba
 *
 * @package Atlas
 * @update 04.06.14
 */

class FactionRanking {
	
	# attributes
	public $id; 
	public $rRanking;
	public $rFaction; 

	public $general; 				# sum of general ranking of the players
	public $generalPosition;
	public $generalVariation;

	public $wealth; 				# credits
	public $wealthPosition;
	public $wealthVariation;

	public $territorial; 			# sectors owned
	public $territorialPosition;
	public $territorialVariation;

	public function getId() { return $this->id; }

	public function commonRender($type = 'general') {
		$r = '';

		switch ($type) {
			case 'general':
				$pos = $this->generalPosition;
				$var = $this->generalVariation; break;
			case 'wealth':
				$pos = $this->wealthPosition;
				$var = $this->wealthVariation; break;
			case 'territorial':
				$pos = $this->territorialPosition;
				$var = $this->territorialVariation; break;
			default: $var = ''; $pos = ''; break;
		}

		$r .= '<div class="player faction color' . $this->rFaction . ' ' . (CTR::$data->get('playerInfo')->get('color') == $this->rFaction ? 'active' : NULL) . '">';
			$r .= '<img src="' . MEDIA . 'faction/flag/flag-' . $this->rFaction . '.png" alt="' . $this->rFaction . '" />';

			$r .= '<span class="title">' . ColorResource::getInfo($this->rFaction, 'government') . '</span>';
			$r .= '<strong class="name">' . ColorResource::getInfo($this->rFaction, 'popularName') . '</strong>';
			$r .= '<span class="experience">';
				switch ($type) {
					case 'general': $r .= Format::number($this->general, -1) . ' points'; break;
					case 'wealth': $r .= Format::number($this->wealth, -1) . ' crédits'; break;
					case 'territorial': $r .= Format::number($this->territorial, -1) . ' secteurs'; break;
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
