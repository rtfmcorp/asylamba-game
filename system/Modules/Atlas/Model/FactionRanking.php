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
namespace Asylamba\Modules\Atlas\Model;

use Asylamba\Classes\Worker\CTR;
use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Demeter\Resource\ColorResource;

class FactionRanking {
	
	# attributes
	public $id; 
	public $rRanking;
	public $rFaction; 

	public $points; 				# accumulated points
	public $pointsPosition;
	public $pointsVariation;
	public $newPoints;

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

	public function commonRender($playerInfo, $type = 'general') {
		$r = '';

		switch ($type) {
			case 'points':
				$pos = $this->pointsPosition;
				$var = $this->pointsVariation; break;
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

		$r .= '<div class="player faction color' . $this->rFaction . ' ' . ($playerInfo->get('color') == $this->rFaction ? 'active' : NULL) . '">';
			$r .= '<img src="' . MEDIA . 'faction/flag/flag-' . $this->rFaction . '.png" alt="' . $this->rFaction . '" class="picto" />';

			$r .= '<span class="title">' . ColorResource::getInfo($this->rFaction, 'government') . '</span>';
			$r .= '<strong class="name">' . ColorResource::getInfo($this->rFaction, 'popularName') . '</strong>';
			$r .= '<span class="experience">';
				switch ($type) {
					case 'points': 
						$r .= Format::number($this->points, -1) . ' points';
						if ($this->newPoints > 0) {
							$r .= ' (+' . Format::number($this->newPoints, -1) . ' points)';
						}
						break;
					case 'general': $r .= Format::number($this->general, -1) . ' points'; break;
					case 'wealth': $r .= Format::number($this->wealth, -1) . ' crédits'; break;
					case 'territorial': $r .= Format::number($this->territorial, -1) . ' points'; break;
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
