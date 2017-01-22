<?php

use Asylamba\Modules\Zeus\Model\Player;
use Asylamba\Classes\Exception\ErrorException;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');
$playerManager = $this->getContainer()->get('zeus.player_manager');
$orbitalBaseManager = $this->getContainer()->get('athena.orbital_base_manager');
$colorManager = $this->getContainer()->get('demeter.color_manager');

# background paralax
echo '<div id="background-paralax" class="embassy"></div>';

# inclusion des elements
include 'embassyElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	include COMPONENT . 'publicity.php';

	if ($request->query->has('player')) {
		$player = $request->query->has('player')
			? $request->query->get('player')
			: $session->get('playerId');
		$ishim = $request->query->has('player') || $request->query->get('player') !== $session->get('playerId')
			? FALSE
			: TRUE;

		$playerBases = $orbitalBaseManager->getPlayerBases($player);

		if (($player_selected = $playerManager->get($player)) && in_array($player_selected->getStatement(), [Player::ACTIVE, Player::INACTIVE, Player::HOLIDAY, Player::BANNED])) {
			$player_ishim = $ishim;
			$ob_selected = $playerBases;
			include COMPONENT . 'embassy/diary/search.php';

			# diaryBases component
			$ob_diaryBases = $playerBases;
			include COMPONENT . 'embassy/diary/bases.php';
		} else {
			// @TODO
			throw new ErrorException('Le joueur a supprimé son compte ou a été défait.');
			//$response->redirect('profil');
		}

		include COMPONENT . 'default.php';

	} else {
		$color = $request->query->has('faction')
			? $request->query->get('faction')
			: $session->get('playerInfo')->get('color');

		$S_COL_1 = $colorManager->getCurrentSession();
		$colorManager->newSession();
		$colorManager->load(array('isInGame' => TRUE));

		$factions = [];
		for ($i = 0; $i < $colorManager->size(); $i++) { 
			$factions[] = $colorManager->get($i)->id;
		}

		$colorManager->changeSession($S_COL_1);

		if (in_array($color, $factions)) {
			# load data
			$S_COL_1 = $colorManager->getCurrentSession();
			$colorManager->newSession();
			$colorManager->load(array('id' => $color));
			$faction = $colorManager->get(0);

			$governmentMembers = $playerManager->getGovernmentMembers($faction->id);

			# include component
			include COMPONENT . 'embassy/faction/nav.php';
			
			include COMPONENT . 'embassy/faction/flag.php';
			include COMPONENT . 'embassy/faction/infos.php';
			include COMPONENT . 'embassy/faction/government.php';

			$eraseColor = $faction->id;
			include COMPONENT . 'faction/data/diplomacy/main.php';

			# close session
			$colorManager->changeSession($S_COL_1);
		} else {
			$this->getContainer()->get('app.response')->redirect('embassy');
		}
	}
echo '</div>';