<?php
class Parser {
	public $parseIcon	= TRUE;
	public $parseLink	= TRUE;
	public $parseSmile	= TRUE;

	public $parsePlayer	= TRUE;
	public $parsePlace	= TRUE;

	public $parseTag 	= TRUE;
	public $parseBigTag = FALSE;

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
		$tl  = '<div class="toolbar">';
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
		$tl .= '</div>';

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
		$pattern = "/(?i)\b((?:https?:\/\/|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}\/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))/";
		$string = preg_replace($pattern, '<a href="$0" target="_blank">$0</a>', $string);
		return $string;
	}

	protected function parseSmile($string) {
		// some actions
		return $string;
	}

	protected function parsePlayer($string) {
		$string = preg_replace_callback(
			'#\[\@(.+)\]#isU', 
			function($m) {
				include_once ZEUS;
				$S_PAM1 = ASM::$pam->getCurrentSession();
				ASM::$pam->newSession(FALSE);

				ASM::$pam->load(array('name' => $m[1]));

				if (ASM::$pam->size() > 0) {
					$player = ASM::$pam->get();
					return '<a href="' . APP_ROOT . 'embassy/player-' . $player->getId() . '" class="color' . $player->getRColor() . ' hb lt" title="voir le profil">' . $player->getName() . '</a>';
				} else {
					return $m[0];
				}

				ASM::$pam->changeSession($S_PAM1);
			}, 
			$string);

		return $string;
	}

	protected function parsePlace($string) {
		$string = preg_replace_callback(
			'#\[\#(.+)\]#isU', 
			function($m) {
				include_once GAIA;
				$S_PLM1 = ASM::$plm->getCurrentSession();
				ASM::$plm->newSession(FALSE);

				ASM::$plm->load(array('id' => $m[1]));

				if (ASM::$plm->size() > 0) {
					$place = ASM::$plm->get();
					if ($place->getTypeOfBase() > 0) {
						return '<a href="' . APP_ROOT . 'map/place-' . $place->getId() . '" class="color' . $place->getPlayerColor() . ' hb lt" title="voir la planète">' . $place->getBaseName() . '</a>';
					} else {
						return '<a href="' . APP_ROOT . 'map/place-' . $place->getId() . '" class="hb lt" title="voir la planète">planète rebelle</a>';
					}
				} else {
					return $m[0];
				}

				ASM::$plm->changeSession($S_PLM1);
			}, 
			$string);

		return $string;
	}

	protected function parseTag($string) {
		$string = preg_replace('#\[b\](.+)\[/b\]#isU', '<strong>$1</strong>', $string);
		$string = preg_replace('#\[i\](.+)\[/i\]#isU', '<em>$1</em>', $string);

		return $string;
	}

	protected function parseBigTag($string) {
		// some actions
		return $string;
	}
}