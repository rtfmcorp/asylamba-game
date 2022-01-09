<?php

use App\Modules\Zeus\Model\Player;
use App\Classes\Exception\ErrorException;

$container = $this->getContainer();
$componentPath = $container->getParameter('component');
$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$playerManager = $this->getContainer()->get(\App\Modules\Zeus\Manager\PlayerManager::class);
$orbitalBaseManager = $this->getContainer()->get(\App\Modules\Athena\Manager\OrbitalBaseManager::class);
$colorManager = $this->getContainer()->get(\App\Modules\Demeter\Manager\ColorManager::class);

# background paralax
echo '<div id="background-paralax" class="embassy"></div>';

# inclusion des elements
include 'embassyElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	include $componentPath . 'publicity.php';

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
			include $componentPath . 'embassy/diary/search.php';

			# diaryBases component
			$ob_diaryBases = $playerBases;
			include $componentPath . 'embassy/diary/bases.php';
		} else {
			// @TODO
			throw new ErrorException('Le joueur a supprimé son compte ou a été défait.');
			//$response->redirect('profil');
		}

		include $componentPath . 'default.php';

	} else {
		$color = $request->query->has('faction')
			? $request->query->get('faction')
			: $session->get('playerInfo')->get('color');

		if (($faction = $colorManager->get($color)) !== null && $faction->isInGame === true) {
			$governmentMembers = $playerManager->getGovernmentMembers($faction->id);
			$factions = $colorManager->getInGameFactions();
			# include component
			include $componentPath . 'embassy/faction/nav.php';
			
			include $componentPath . 'embassy/faction/flag.php';
			include $componentPath . 'embassy/faction/infos.php';
			include $componentPath . 'embassy/faction/government.php';

			$eraseColor = $faction->id;
			include $componentPath . 'faction/data/diplomacy/main.php';
		} else {
			$this->getContainer()->get('app.response')->redirect('embassy');
		}
	}
echo '</div>';
