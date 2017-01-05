<?php
# routeFinancial component
# in athena package

# détail les routes commerciales par base

# require
	# [{orbitalBase}]			ob_routeFinancial

# view part

use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Athena\Model\CommercialRoute;

$commercialRouteManager = $this->getContainer()->get('athena.commercial_route_manager');

echo '<div class="component financial">';
	echo '<div class="head skin-1">';
		echo '<img src="' . MEDIA . 'financial/commercial-route.png" alt="route commerciale" />';
		echo '<h2>Commerce</h2>';
		echo '<em>Revenus des routes commerciales par planète</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
		echo '<ul class="list-type-1">';
			foreach ($ob_routeFinancial as $base) {
				$S_CRM1 = $commercialRouteManager->getCurrentSession();
				$commercialRouteManager->changeSession($base->routeManager);

				$nbRoute = 0;
				for ($k = 0; $k < $commercialRouteManager->size(); $k++) {
					if ($commercialRouteManager->get($k)->getStatement() == CommercialRoute::ACTIVE) {
						$nbRoute++;
					}
				}
				$routeIncome = 0;
				for ($k = 0; $k < $commercialRouteManager->size(); $k++) {
					if ($commercialRouteManager->get($k)->getStatement() == CommercialRoute::ACTIVE) {
						$routeIncome += $commercialRouteManager->get($k)->getIncome();
					}
				}

				echo '<li>';
					if ($nbRoute > 0) {
						echo '<span class="buttons">';
							echo '<a href="#" class="sh" data-target="rc-base-' . $base->getId() . '">↓</a>';
						echo '</span>';
					}
					echo '<span class="label">' . $base->getName() . ' [' . $nbRoute . ' route' . Format::addPlural($nbRoute) . ']</span>';
					echo '<span class="value">';
						echo Format::numberFormat($routeIncome);
						if ($rcBonus > 0) {
							echo '<span class="bonus">+' . Format::numberFormat($routeIncome * $rcBonus / 100) . '</span>';
						}
						echo '<img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" />';
					echo '</span>';

					if ($nbRoute > 0) {
						echo '<ul class="sub-list-type-1" id="rc-base-' . $base->getId() . '">';
							for ($k = 0; $k < $commercialRouteManager->size(); $k++) {
								$route = $commercialRouteManager->get($k);
								if ($commercialRouteManager->get($k)->getStatement() == CommercialRoute::ACTIVE) {
									echo '<li>';
										$rBaseName = ($route->getBaseName1() == $base->getName()) ? $route->getBaseName2(): $route->getBaseName1();
										echo '<span class="label">' . $rBaseName . '</span>';
										echo '<span class="value">' . Format::numberFormat($route->getIncome()) . '</span>';
									echo '</li>';
								}
							}
						echo '</ul>';
					}
				echo '</li>';

				$commercialRouteManager->changeSession($S_CRM1);
			}

			echo '<li class="strong">';
				echo '<span class="label">total des routes commerciales</span>';
				echo '<span class="value">';
					echo Format::numberFormat($financial_totalRouteIncome);
					if ($rcBonus > 0) {
						echo '<span class="bonus">+' . Format::numberFormat($financial_totalRouteIncomeBonus) . '</span>';
					}
					echo '<img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" />';
				echo '</span>';
			echo '</li>';
		echo '</ul>';

		echo '<p class="info">La colonne « Commerce » est un compte rendu de la totalité des revenus de vos routes commerciales. Pour gérer les 
		recettes de vos routes commerciales, il vous est nécessaire de vous rendre sur votre plateforme commerciale afin de créer ou supprimer 
		des routes. </p>';
		echo '</div>';
	echo '</div>';
echo '</div>';