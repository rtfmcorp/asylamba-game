<?php
# spatioport component
# in athena.bases package

# affichage du spatioport

# require
	# {orbitalBase}		ob_spatioport

use Asylamba\Modules\Athena\Resource\OrbitalBaseResource;
use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Library\Chronos;
use Asylamba\Modules\Zeus\Model\PlayerBonus;
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Modules\Gaia\Resource\PlaceResource;
use Asylamba\Classes\Library\Game;
use Asylamba\Modules\Athena\Model\CommercialRoute;

$container = $this->getContainer();
$appRoot = $container->getParameter('app_root');
$mediaPath = $container->getParameter('media');
$componentPath = $container->getParameter('component');
$orbitalBaseHelper = $this->getContainer()->get(\Asylamba\Modules\Athena\Helper\OrbitalBaseHelper::class);
$request = $this->getContainer()->get('app.request');
$commercialRouteManager = $this->getContainer()->get(\Asylamba\Modules\Athena\Manager\CommercialRouteManager::class);
$database = $this->getContainer()->get(\Asylamba\Classes\Database\Database::class);
$session = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class);
$sessionToken = $session->get('token');
$entityManager = $this->getContainer()->get(\Asylamba\Classes\Entity\EntityManager::class);

$nMaxCR = $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::SPATIOPORT, 'level', $ob_spatioport->getLevelSpatioport(), 'nbRoutesMax');
$nCRWaitingForOther = 0; $nCRWaitingForMe = 0;
$nCROperational = 0; $nCRInStandBy = 0;
$nCRInDock = 0;
$totalIncome = 0;

$routes = array_merge(
	$commercialRouteManager->getByBase($ob_spatioport->getId()),
	$commercialRouteManager->getByDistantBase($ob_spatioport->getId())
);

if (count($routes) > 0) {
	foreach ($routes as $route) {
		if ($route->getStatement() == CommercialRoute::PROPOSED AND $route->getPlayerId1() == $session->get('playerId')) {
			$nCRWaitingForOther++;
		} elseif ($route->getStatement() == CommercialRoute::PROPOSED AND $route->getPlayerId1() != $session->get('playerId')) {
			$nCRWaitingForMe++;
		} elseif ($route->getStatement() == CommercialRoute::ACTIVE) {
			$nCROperational++;
			$totalIncome += $route->getIncome();
		} elseif ($route->getStatement() == CommercialRoute::STANDBY) {
			$nCRInStandBy++;
		}
	}
	$nCRInDock = $nCROperational + $nCRInStandBy + $nCRWaitingForOther;
}
$inGameFactions = $this->getContainer()->get(\Asylamba\Modules\Demeter\Manager\ColorManager::class)->getInGameFactions();

$factions = [];
foreach ($inGameFactions as $inGameFaction) { 
	$factions[] = $inGameFaction->id;
}

# view
echo '<div class="component building rc">';
	echo '<div class="head skin-1">';
		echo '<img src="' . $mediaPath . 'orbitalbase/spatioport.png" alt="" />';
		echo '<h2>' . $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::SPATIOPORT, 'frenchName') . '</h2>';
		echo '<em>Niveau ' . $ob_spatioport->getLevelSpatioport() . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			$active = (!$request->query->has('mode') || $request->query->get('mode') == 'list') ? 'active' : '';
			echo '<a href="' . $appRoot . 'bases/view-spatioport/mode-list" class="nav-element ' . $active . '">';
				echo '<img src="' . $mediaPath . 'map/option/market.png" alt="" />';
				echo '<strong>Routes commerciales</strong>';
				echo '<em>Gérez vos routes commerciales</em>';
			echo '</a>';

			$active = ($request->query->get('mode') == 'search') ? 'active' : '';
			echo '<a href="' . $appRoot . 'bases/view-spatioport/mode-search" class="nav-element ' . $active . '">';
				echo '<img src="' . $mediaPath . 'map/option/radio.png" alt="" />';
				echo '<strong>Recherche</strong>';
				echo '<em>Trouvez des partenaires commerciaux</em>';
			echo '</a>';

			echo '<hr />';

			echo '<div class="number-box">';
				echo '<span class="label">routes commerciales</span>';
				echo '<span class="value">' . $nCRInDock . ' / ' . $nMaxCR . '</span>';

				echo '<span class="progress-bar">';
				echo '<span style="width:' . Format::percent($nCRInDock, $nMaxCR) . '%;" class="content"></span>';
			echo '</div>';

			echo '<div class="number-box ' . (($nCROperational == 0) ? 'grey' : '') . '">';
				echo '<span class="label">routes commerciales actives</span>';
				echo '<span class="value">' . $nCROperational . '</span>';
			echo '</div>';

			echo '<div class="number-box ' . (($nCRWaitingForOther == 0) ? 'grey' : '') . '">';
				echo '<span class="label">routes commerciales en attente</span>';
				echo '<span class="value">' . $nCRWaitingForOther . '</span>';
			echo '</div>';

			echo '<div class="number-box ' . (($nCRWaitingForMe == 0) ? 'grey' : '') . '">';
				echo '<span class="label">propositions commerciales</span>';
				echo '<span class="value">' . $nCRWaitingForMe . '</span>';
			echo '</div>';

			if ($nCRInStandBy > 0) {
				echo '<div class="number-box">';
					echo '<span class="label">routes commerciales bloquées</span>';
					echo '<span class="value">' . $nCRInStandBy . '</span>';
				echo '</div>';
			}

			echo '<hr />';

			echo '<div class="number-box">';
				echo '<span class="label">revenu total de cette base</span>';
				echo '<span class="value">';
					echo Format::numberFormat($totalIncome);
					if ($session->get('playerBonus')->get(PlayerBonus::COMMERCIAL_INCOME) != 0) {
						echo '<span class="bonus">+ ' .  Format::numberFormat($session->get('playerBonus')->get(PlayerBonus::COMMERCIAL_INCOME) * $totalIncome / 100)  . '</span>';
					}
					echo ' <img src="' . $mediaPath . 'resources/credit.png" class="icon-color" />';
				echo '</span>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

if ($request->query->get('mode') === 'search') {
	echo '<div class="component new-message">';
		echo '<div class="head skin-2">';
			echo '<h2>Recherche</h2>';
		echo '</div>';
		echo '<div class="fix-body">';
			echo '<div class="body">';
				echo '<form action="' . $appRoot . 'bases/view-spatioport/mode-search/show-result" method="POST" />';
					echo '<h4>Chercher des partenaires commerciaux...</h4>';
					foreach ($factions as $i) {
						echo '<p><label for="ckb-faction-' . $i . '">';
							echo '<input type="checkbox" name="faction-' . $i . '" id="ckb-faction-' . $i . '" ' . (!$request->query->has('show') || $request->request->has('faction-' . $i) ? 'checked' : NULL) . ' /> ';
							echo ColorResource::getInfo($i, 'demonym');
						echo '</label></p>';
					}

					echo '<h4>A une distance...</h4>';

					echo '<p><label for="search-rc-min-dist">Minimum</label></p>';
					echo '<p class="input input-text"><input type="number" id="search-rc-min-dist" name="min-dist" value="' . ($request->request->has('min-dist') ? $request->request->get('min-dist') : 75) . '" /></p>';

					echo '<p><label for="search-rc-max-dist">Maximum</label></p>';
					echo '<p class="input input-text"><input type="number" id="search-rc-max-dist" name="max-dist" value="' . ($request->request->has('max-dist') ? $request->request->get('max-dist') : 125) . '" /></p>';

					echo '<p><button>Rechercher</button></p>';
				echo '</form>';
			echo '</div>';
		echo '</div>';
	echo '</div>';

	if ($request->query->get('show') === 'result') {
		$min = $request->request->has('min-dist') ? abs(intval($request->request->get('min-dist'))) : 75;
		$max = $request->request->has('max-dist') ? abs(intval($request->request->get('max-dist'))) : 75;

		$checkedFactions = [0];
		foreach ($factions as $i) {
			if ($request->request->has('faction-' . $i)) {
				$checkedFactions[] = $i;
			}
		}

		$qr = $database->prepare('SELECT 
				pa.rColor AS playerColor,
				pa.avatar AS playerAvatar,
				pa.name AS playerName,
				ob.name AS baseName,
				ob.rPlace AS rPlace,
				(FLOOR(SQRT(POW(? - s.xPosition, 2) + POW(? - s.yPosition, 2)))) AS distance
			FROM orbitalBase AS ob
			LEFT JOIN player AS pa
				ON ob.rPlayer = pa.id
			LEFT JOIN place AS p
				ON ob.rPlace = p.id
			LEFT JOIN system AS s
				ON p.rSystem = s.id
			LEFT JOIN sector AS se
				ON s.rSector = se.id
			WHERE
				pa.id != ?
				AND ob.levelSpatioport > 0
				AND (FLOOR(SQRT(POW(? - s.xPosition, 2) + POW(? - s.yPosition, 2)))) >= ?
				AND (FLOOR(SQRT(POW(? - s.xPosition, 2) + POW(? - s.yPosition, 2)))) <= ?
				AND pa.rColor in (' . implode(',', $checkedFactions) . ')
			ORDER BY distance DESC
			LIMIT 0, 40'
		);
		$qr->execute([
			$ob_spatioport->xSystem, $ob_spatioport->ySystem,
			$session->get('playerId'),
			$ob_spatioport->xSystem, $ob_spatioport->ySystem, $min,
			$ob_spatioport->xSystem, $ob_spatioport->ySystem, $max
		]);
		$aw = $qr->fetchAll();

		echo '<div class="component player rank">';
			echo '<div class="head skin-2">';
				echo '<h2>Résultats</h2>';
			echo '</div>';
			echo '<div class="fix-body">';
				echo '<div class="body">';
					foreach ($aw as $base) {
						echo '<a href="' . $appRoot . 'map/place-' . $base['rPlace'] . '" class="player color' . $base['playerColor'] . '">';
							echo '<img src="' . $mediaPath . 'avatar/small/' . $base['playerAvatar'] . '.png" alt="" class="picto">';
							echo '<span class="title">' . $base['playerName'] . '</span>';
							echo '<strong class="name">' . $base['baseName'] . '</strong>';
							echo '<span class="experience">' . $base['distance'] . ' al.</span>';
						echo '</a>';
					}

					if (count($aw) == 0) {
						echo '<p><em>Aucun partenaire commercial trouvé selon les critères de recherche fournis.</em></p>';
					}
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
} else {
	$j = 0;
	foreach ($routes as $rc) {
		if ($rc->getStatement() == CommercialRoute::PROPOSED && $rc->getPlayerId2() == $session->get('playerId')) {
			$base1  = '<div class="base">';
				$base1 .= '<img src="' . $mediaPath . 'map/place/place1-' . Game::getSizeOfPlanet($rc->getPopulation1()) . '.png" alt="' . $ob_spatioport->getName() . '" class="place" />';
				$base1 .= '' . PlaceResource::get($rc->baseType1, 'name') . ' <a href="' . $appRoot . 'map/place-' . $rc->getROrbitalBase() . '">' . $rc->getBaseName1() . '</a><br />';
				$base1 .= 'de <a href="' . $appRoot . 'embassy/player-' . $rc->getPlayerId1() . '">' . $rc->getPlayerName1() . '</a><br />';
				$base1 .= Format::numberFormat($rc->getPopulation1()) . ' millions de population';
			$base1 .= '</div>';

			$base2  = '<div class="base">';
				$base2 .= '<img src="' . $mediaPath . 'map/place/place1-' . Game::getSizeOfPlanet($rc->getPopulation2()) . '.png" alt="' . $ob_spatioport->getName() . '" class="place" />';
				$base2 .= '' . PlaceResource::get($rc->baseType2, 'name') . ' <a href="' . $appRoot . 'map/place-' . $rc->getROrbitalBaseLinked() . '">' . $rc->getBaseName2() . '</a><br />';
				$base2 .= 'de <a href="' . $appRoot . 'embassy/player-' . $rc->getPlayerId2() . '">' . $rc->getPlayerName2() . '</a><br />';
				$base2 .= Format::numberFormat($rc->getPopulation2()) . ' millions de population';
			$base2 .= '</div>';

			echo '<div class="component rc">';
				echo '<div class="head skin-5">';
					echo ($j == 0) ? '<h2>Propositions</h2>' : '';
				echo '</div>';
				echo '<div class="fix-body">';
					echo '<div class="body">';
						echo '<div class="tool">';
							if ($rc->getPrice() > $session->get('playerInfo')->get('credit')) {
								echo '<span><a href="#">pas assez de crédits pour accepter</a></span>';
							} elseif ($nCRInDock >= $nMaxCR) {
								echo '<span><a href="#">pas d\'emplacement libre pour accepter</a></span>';
							} else {
								$price = $rc->getPrice();
								if ($session->get('playerInfo')->get('color') == ColorResource::NEGORA) {
									# bonus if the player is from Negore
									$price -= round($price * ColorResource::BONUS_NEGORA_ROUTE / 100);
								}
								echo '<span><a href="' . Format::actionBuilder('acceptroute', $sessionToken, ['base' => $rc->getROrbitalBaseLinked(), 'route' => $rc->getId()]) . '">accepter pour ' . Format::numberFormat($price) . ' crédits</a></span>';
							}
							echo '<span><a href="' . Format::actionBuilder('refuseroute', $sessionToken, ['base' => $rc->getROrbitalBaseLinked(), 'route' => $rc->getId()]) . '" class="hb lt" title="refuser l\'offre">x</a></span>';
						echo '</div>';

						echo '<div class="number-box grey">';
							echo '<span class="label">Etat de la route commerciale</span>';
							echo '<span class="value">';
								echo 'En attente';
							echo '</span>';
						echo '</div>';

						echo '<div class="rc ' . (($rc->getStatement() != CommercialRoute::ACTIVE) ? 'no-tax' : '') . '" style="height: ' . (370 + $rc->getDistance()) . 'px;">';
							echo ($rc->getPlayerId1() == $session->get('playerId')) ? $base2 : $base1;

							echo '<ul class="general">';
								echo '<li>Distance <strong>' . $rc->getDistance() . ' al.</strong></li>';
								echo '<li>Prix <strong>';
									echo Format::numberFormat($rc->getPrice());
									if ($session->get('playerInfo')->get('color') == ColorResource::NEGORA) {
										# bonus if the player is from Negore
										echo '<span class="bonus">- ' .  Format::numberFormat(round(ColorResource::BONUS_NEGORA_ROUTE * $rc->getPrice() / 100))  . '</span>';
									}
									echo ' <img src="' . $mediaPath . 'resources/credit.png" class="icon-color" />';
								echo '</strong></li>';
								echo '<li>Estimation du revenu par relève<strong>';
									echo Format::numberFormat($rc->getIncome());
									if ($session->get('playerBonus')->get(PlayerBonus::COMMERCIAL_INCOME) != 0) {
										echo '<span class="bonus">+ ' .  Format::numberFormat($session->get('playerBonus')->get(PlayerBonus::COMMERCIAL_INCOME) * $rc->getIncome() / 100)  . '</span>';
									}
									echo ' <img src="' . $mediaPath . 'resources/credit.png" class="icon-color" />';		
								echo '</strong></li>';
								echo '<li>Population touchée <strong>' . Format::numberFormat($rc->getPopulation1() + $rc->getPopulation2()) . ' millions</strong></li>';

								if (in_array($rc->getStatement(), array(CommercialRoute::ACTIVE, CommercialRoute::STANDBY))) {
									echo '<li>En service depuis <br />' . Chronos::transform($rc->dCreation) . '</li>';
								}
							echo '</ul>';

							echo ($rc->getPlayerId1() == $session->get('playerId')) ? $base1 : $base2;
						echo '</div>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
			$j++;
		}
	}

	$j = 0;
	foreach ($routes as $rc) {
		if ($rc->getStatement() != CommercialRoute::PROPOSED || $rc->getPlayerId2() != $session->get('playerId')) {
			$base1  = '<div class="base">';
				$base1 .= '<img src="' . $mediaPath . 'map/place/place1-' . Game::getSizeOfPlanet($rc->getPopulation1()) . '.png" class="place" />';
				$base1 .= '' . PlaceResource::get($rc->baseType1, 'name') . ' <a href="' . $appRoot . 'map/place-' . $rc->getROrbitalBase() . '">' . $rc->getBaseName1() . '</a><br />';
				$base1 .= 'de <a href="' . $appRoot . 'embassy/player-' . $rc->getPlayerId1() . '">' . $rc->getPlayerName1() . '</a><br />';
				$base1 .= Format::numberFormat($rc->getPopulation1()) . ' millions de population';
			$base1 .= '</div>';

			$base2  = '<div class="base">';
				$base2 .= '<img src="' . $mediaPath . 'map/place/place1-' . Game::getSizeOfPlanet($rc->getPopulation2()) . '.png" class="place" />';
				$base2 .= '' . PlaceResource::get($rc->baseType2, 'name') . ' <a href="' . $appRoot . 'map/place-' . $rc->getROrbitalBaseLinked() . '">' . $rc->getBaseName2() . '</a><br />';
				$base2 .= 'de <a href="' . $appRoot . 'embassy/player-' . $rc->getPlayerId2() . '">' . $rc->getPlayerName2() . '</a><br />';
				$base2 .= Format::numberFormat($rc->getPopulation2()) . ' millions de population';
			$base2 .= '</div>';

			echo '<div class="component rc">';
				echo '<div class="head skin-5">';
					echo ($j == 0) ? '<h2>Routes commerciales</h2>' : '';
				echo '</div>';
				echo '<div class="fix-body">';
					echo '<div class="body">';
						echo '<div class="tool">';
						if ($rc->getStatement() == CommercialRoute::PROPOSED) {
							echo '<span><a href="' . Format::actionBuilder('cancelroute', $sessionToken, ['base' => $ob_spatioport->getId(), 'route' => $rc->getId()]) . '">annuler la proposition commerciale</a></span>';
						} else {
							echo '<span><a href="' . Format::actionBuilder('deleteroute', $sessionToken, ['base' => $ob_spatioport->getId(), 'route' => $rc->getId()]) . '">démanteler la route commerciale</a></span>';
						}
						echo '</div>';

						echo '<div class="number-box ' . (($rc->getStatement() != CommercialRoute::ACTIVE) ? 'grey' : '') . '">';
							echo '<span class="label">Etat de la route commerciale</span>';
							echo '<span class="value">';
								switch ($rc->getStatement()) {
									case CommercialRoute::PROPOSED:	echo 'En attente'; break;		
									case CommercialRoute::ACTIVE:	echo 'En activité'; break;		
									case CommercialRoute::STANDBY:	echo 'Gelée'; break;		
									default: break;
								}
							echo '</span>';
						echo '</div>';

						echo '<div class="rc ' . (($rc->getStatement() != CommercialRoute::ACTIVE) ? 'no-tax' : '') . '" style="height: ' . (370 + $rc->getDistance()) . 'px;">';
							echo ($rc->getPlayerId1() == $session->get('playerId')) ? $base2 : $base1;

							echo '<ul class="general">';
								echo '<li>Distance <strong>' . $rc->getDistance() . ' al.</strong></li>';
								echo '<li>Revenu par relève ' . (($rc->getStatement() != CommercialRoute::ACTIVE) ? '[non effectif]' : '') . '<strong>';
									echo Format::numberFormat($rc->getIncome());
									if ($session->get('playerBonus')->get(PlayerBonus::COMMERCIAL_INCOME) != 0) {
										echo '<span class="bonus">+ ' .  Format::numberFormat($session->get('playerBonus')->get(PlayerBonus::COMMERCIAL_INCOME) * $rc->getIncome() / 100)  . '</span>';
									}
									echo ' <img src="' . $mediaPath . 'resources/credit.png" class="icon-color" />';		
								echo '</strong></li>';
								echo '<li>Population touchée <strong>' . Format::numberFormat($rc->getPopulation1() + $rc->getPopulation2()) . ' millions</strong></li>';

								if (in_array($rc->getStatement(), array(CommercialRoute::ACTIVE, CommercialRoute::STANDBY))) {
									echo '<li>En service depuis <br />' . Chronos::transform($rc->dCreation) . '</li>';
								}
							echo '</ul>';

							echo ($rc->getPlayerId1() == $session->get('playerId')) ? $base1 : $base2;
						echo '</div>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
			$j++;
		}
	}
}

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>À propos</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<p class="long-info">' . $orbitalBaseHelper->getBuildingInfo(OrbitalBaseResource::SPATIOPORT, 'description') . '</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';

if (count($routes) == 0) {
	include $componentPath . 'default.php';
}

$entityManager->clear(CommercialRoute::class);
