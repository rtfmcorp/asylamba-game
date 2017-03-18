<?php

namespace Asylamba\Classes\Library;

use Asylamba\Modules\Zeus\Manager\PlayerManager;
use Asylamba\Modules\Gaia\Manager\PlaceManager;

class Parser {
	/** @var PlaceManager **/
	protected $placeManager;
	/** @var PlayerManager **/
	protected $playerManager;
	
	public $parseIcon	= TRUE;
	public $parseLink	= TRUE;
	public $parseSmile	= TRUE;

	public $parsePlayer	= TRUE;
	public $parsePlace	= TRUE;

	public $parseTag 	= TRUE;
	public $parseBigTag = FALSE;

	/**
	 * @param PlaceManager $placeManager
	 * @param PlayerManager $playerManager
	 */
	public function __construct(PlaceManager $placeManager, PlayerManager $playerManager)
	{
		$this->playerManager = $playerManager;
		$this->placeManager = $placeManager;
	}
	
	public function parse($string) {
		$string = $this->protect($string);

		if ($this->parseLink)	{ $string = $this->parseLink($string); }
		if ($this->parseIcon)	{ $string = $this->parseIcon($string); }
		if ($this->parseSmile)	{ $string = $this->parseSmile($string); }
		if ($this->parsePlayer)	{ $string = $this->parsePlayer($string); }
		if ($this->parsePlace)	{ $string = $this->parsePlace($string); }
		if ($this->parseTag)	{ $string = $this->parseTag($string); }
		if ($this->parseBigTag)	{ $string = $this->parseBigTag($string); }

		return $string;
	}

	public static function protect($string) {
		$string = trim($string);
		$string = htmlspecialchars($string);
		$string = nl2br($string);

		return $string;
	}

	public function getToolbar() {
		$tl  = '<span class="toolbar">';
			if ($this->parseTag) {
				$tl .= '<button data-tag="bl">Gras</button>';
				$tl .= '<button data-tag="it">Italique</button>';
			}
			if ($this->parseIcon) {
		#		$tl .= '<button data-tag="ic">Icône</button>';
			}
			if ($this->parsePlayer) {
				$tl .= '<button data-tag="py">Joueur</button>';
			}
			if ($this->parsePlace) {
				$tl .= '<button data-tag="pl">Planète</button>';
			}
		$tl .= '</span>';

		return $tl;
	}

	protected function parseIcon($string) {
		$string = preg_replace('#\[pa\]#', '<img src="' . MEDIA . 'resources/pa.png" alt="pa" class="hb lt icon-color" title="point d\'action" />', $string);
		$string = preg_replace('#\[pev\]#', '<img src="' . MEDIA . 'resources/pev.png" alt="pev" class="hb lt icon-color" title="point équivalent vaisseaux" />', $string);
		$string = preg_replace('#\[credit\]#', '<img src="' . MEDIA . 'resources/credit.png" alt="credit" class="hb lt icon-color" title="crédit" />', $string);
		$string = preg_replace('#\[ressource\]#', '<img src="' . MEDIA . 'resources/resource.png" alt="resource" class="hb lt icon-color" title="ressource" />', $string);
		$string = preg_replace('#\[releve\]#', '<img src="' . MEDIA . 'resources/time.png" alt="time" class="hb lt icon-color" title="relève" />', $string);

		$string = preg_replace('#\[attaque\]#', '<img src="' . MEDIA . 'resources/attack.png" alt="attack" class="hb lt icon-color" title="point d\'attaque" />', $string);
		$string = preg_replace('#\[vie\]#', '<img src="' . MEDIA . 'resources/life.png" alt="life" class="hb lt icon-color" title="point de vie" />', $string);
		$string = preg_replace('#\[defense\]#', '<img src="' . MEDIA . 'resources/defense.png" alt="defense" class="hb lt icon-color" title="point de défense" />', $string);
		$string = preg_replace('#\[vitesse\]#', '<img src="' . MEDIA . 'resources/speed.png" alt="speed" class="hb lt icon-color" title="point de vitesse" />', $string);

		return $string;
	}

	protected function parseLink($string) {
		return preg_replace_callback(
			"/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))/",
			function($m) {
				$url = $m[0];
				$str = $url;
				$str = preg_replace('#^https?://#', '', $str);
				$str = strlen($str) > 32 ? substr($str, 0, 32) . '...' : $str;
				return '<a href="' . $url . '" target="_blank">' . $str . '</a>';
			},
			$string
		);
	}

	protected function parseSmile($string) {
		return $string;
	}

	protected function parsePlayer($string) {
		return preg_replace_callback(
			'#\[\@(.+)\]#isU',
			function($m) {
				return
					(($player = $this->playerManager->getByName($m[1])) !== null)
					? '<a href="' . APP_ROOT . 'embassy/player-' . $player->getId() . '" class="color' . $player->getRColor() . ' hb lt" title="voir le profil">' . $player->getName() . '</a>'
					: $m[0]
				;
			},
			$string
		);
	}

	protected function parsePlace($string) {
		return preg_replace_callback(
			'#\[\#(.+)\]#isU',
			function($m) {
				if (($place = $this->placeManager->get($m[1]))) {
					if ($place->getTypeOfBase() > 0) {
						return '<a href="' . APP_ROOT . 'map/place-' . $place->getId() . '" class="color' . $place->getPlayerColor() . ' hb lt" title="voir la planète">' . $place->getBaseName() . '</a>';
					} else {
						return '<a href="' . APP_ROOT . 'map/place-' . $place->getId() . '" class="hb lt" title="voir la planète">planète rebelle</a>';
					}
				}
				return $m[0];
			},
			$string
		);
	}

	protected function parseTag($string) {
		$string = preg_replace('#\[b\](.+)\[/b\]#isU', '<strong>$1</strong>', $string);
		$string = preg_replace('#\[i\](.+)\[/i\]#isU', '<em>$1</em>', $string);

		return $string;
	}

	protected function parseBigTag($string) {
		return $string;
	}
}
