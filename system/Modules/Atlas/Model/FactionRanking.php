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
	
	/**
	 * @param int $id
	 * @return FactionRanking
	 */
	public function setId($id)
	{
		$this->id = $id;
		
		return $this;
	}

	/**
	 * @return int
	 */
	public function getId()
	{ 
		return $this->id;
	}

	/**
	 * @param int $rankingId
	 * @return FactionRanking
	 */
	public function setRankingId($rankingId)
	{
		$this->rRanking = $rankingId;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getRankingId()
	{
		return $this->rRanking;
	}
	
	/**
	 * @param int $factionId
	 * @return FactionRanking
	 */
	public function setFactionId($factionId)
	{
		$this->rFaction = $factionId;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getFactionId()
	{
		return $this->rFaction;
	}
	
	/**
	 * @param int $points
	 * @return FactionRanking
	 */
	public function setPoints($points)
	{
		$this->points = $points;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getPoints()
	{
		return $this->points;
	}
	
	/**
	 * @param int $pointsPosition
	 * @return FactionRanking
	 */
	public function setPointsPosition($pointsPosition)
	{
		$this->pointsPosition = $pointsPosition;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getPointsPosition()
	{
		return $this->pointsPosition;
	}
	
	/**
	 * @param int $pointsVariation
	 * @return FactionRanking
	 */
	public function setPointsVariation($pointsVariation)
	{
		$this->pointsVariation = $pointsVariation;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getPointsVariation()
	{
		return $this->pointsVariation;
	}
	
	/**
	 * @param int $newPoints
	 * @return FactionRanking
	 */
	public function setNewPoints($newPoints)
	{
		$this->newPoints = $newPoints;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getNewPoints()
	{
		return $this->newPoints;
	}
	
	/**
	 * @param int $general
	 * @return FactionRanking
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
	
	/**
	 * @param int $generalPosition
	 * @return FactionRanking
	 */
	public function setGeneralPosition($generalPosition)
	{
		$this->generalPosition = $generalPosition;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getGeneralPosition()
	{
		return $this->generalPosition;
	}
	
	/**
	 * @param int $generalVariation
	 * @return FactionRanking
	 */
	public function setGeneralVariation($generalVariation)
	{
		$this->generalVariation = $generalVariation;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getGeneralVariation()
	{
		return $this->generalVariation;
	}
	
	/**
	 * @param int $wealth
	 * @return FactionRanking
	 */
	public function setWealth($wealth)
	{
		$this->wealth = $wealth;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getWealth()
	{
		return $this->wealth;
	}
	
	/**
	 * @param int $wealthPosition
	 * @return FactionRanking
	 */
	public function setWealthPosition($wealthPosition)
	{
		$this->wealthPosition = $wealthPosition;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getWealthPosition()
	{
		return $this->wealthPosition;
	}
	
	/**
	 * @param int $wealthVariation
	 * @return FactionRanking
	 */
	public function setWealthVariation($wealthVariation)
	{
		$this->wealthVariation = $wealthVariation;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getWealthVariation()
	{
		return $this->wealthVariation;
	}
	
	/**
	 * @param int $territorial
	 * @return FactionRanking
	 */
	public function setTerritorial($territorial)
	{
		$this->territorial = $territorial;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getTerritorial()
	{
		return $this->territorial;
	}
	
	/**
	 * @param int $territorialPosition
	 * @return FactionRanking
	 */
	public function setTerritorialPosition($territorialPosition)
	{
		$this->territorialPosition = $territorialPosition;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getTerritorialPosition()
	{
		return $this->territorialPosition;
	}
	
	/**
	 * @param int $territorialVariation
	 * @return FactionRanking
	 */
	public function setTerritorialVariation($territorialVariation)
	{
		$this->territorialVariation = $territorialVariation;
		
		return $this;
	}
	
	/**
	 * @return int
	 */
	public function getTerritorialVariation()
	{
		return $this->territorialVariation;
	}
	
	public function commonRender($playerInfo, string $mediaPath, string $type = 'general') {
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
			$r .= '<img src="' . $mediaPath . 'faction/flag/flag-' . $this->rFaction . '.png" alt="' . $this->rFaction . '" class="picto" />';

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
