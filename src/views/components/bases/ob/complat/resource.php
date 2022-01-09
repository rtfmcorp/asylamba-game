<?php

use App\Classes\Library\Format;
use App\Modules\Athena\Model\CommercialShipping;
use App\Modules\Athena\Resource\ShipResource;

$container = $this->getContainer();
$appRoot = $container->getParameter('app_root');
$mediaPath = $container->getParameter('media');
$sessionToken = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class)->get('token');

echo '<div class="component new-message market-sell">';
	echo '<div class="head skin-4 sh">';
		echo '<img src="' . $mediaPath . 'resources/resource.png" alt="ressource" class="main" />';
		echo '<h2>Envoi de ressources</h2>';
		echo '<em>à une autre base</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<form action="' . Format::actionBuilder('giveresource', $sessionToken, ['baseid' => $ob_compPlat->rPlace]) . '" method="post">';
				# base
				echo '<p><label for="send-resources-target">Base destinataire</label></p>';
				echo '<input class="autocomplete-hidden" type="hidden" name="otherbaseid" />';
				echo '<p class="input input-text"><input type="text" id="send-resources-target" class="autocomplete-orbitalbase" name="name" autocomplete="off" /></p>';

				# form de vente
				echo '<div class="sell-form" data-shipcom-size="' . CommercialShipping::WEDGE . '" data-resource-rate="1" data-max-quantity="' . $ob_compPlat->resourcesStorage . '" data-rate="0" data-min-price="0">';
					echo '<div class="label-box">';
						echo '<span class="label">Ressources</span>';
						echo '<span class="value">' . Format::numberFormat($ob_compPlat->resourcesStorage) . '</span>';
						echo '<img class="icon-color" alt="ressources" src="' . $mediaPath . 'resources/resource.png">';
					echo '</div>';

					echo '<div class="label-box sf-quantity">';
						echo '<label for="send-resources-quantity" class="label">Quantité</label>';
						echo '<input id="send-resources-quantity" class="value" type="text" name="quantity" autocomplete="off" />';
						echo '<img class="icon-color" alt="ressources" src="' . $mediaPath . 'resources/resource.png">';
					echo '</div>';

					echo '<hr />';

					echo '<div class="label-box sf-comship">';
						echo '<span class="label">Vaisseaux</span>';
						echo '<span class="value"></span>';
						echo '<img class="icon-color" alt="vaisseaux transports" src="' . $mediaPath . 'resources/transport.png">';
					echo '</div>';

					echo '<hr />';

					echo '<p><input type="submit" value="Envoyer" /></p>';
				echo '</div>';
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component new-message market-sell">';
	echo '<div class="head skin-4">';
		echo '<img src="' . $mediaPath . 'orbitalbase/dock2.png" alt="vaisseaux" class="main" />';
		echo '<h2>Envoi de vaisseaux</h2>';
		echo '<em>à une autre base</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<form action="' . Format::actionBuilder('giveships', $sessionToken, ['baseid' => $ob_compPlat->getId()]) . '" method="post">';
				echo '<p><label for="send-ships-target">Base destinataire</label></p>';
				echo '<input class="autocomplete-hidden" type="hidden" name="otherbaseid" />';
				echo '<p class="input input-text"><input type="text" id="send-ships-target" class="autocomplete-orbitalbase" name="name" autocomplete="off" /></p>';

				foreach ($ob_compPlat->shipStorage as $key => $ship) {
					if ($ship > 0) {
						echo '<div class="queue sh" data-target="sell-ships-' . $key . '">';
							echo '<div class="item">';
								echo '<img class="picto" src="' . $mediaPath . 'ship/picto/ship' . $key . '.png" alt="" />';
								echo '<strong>' . ShipResource::getInfo($key, 'codeName') . '</strong>';
								echo '<em>' . ShipResource::getInfo($key, 'name') . '</em>';
								echo '<em>' . ShipResource::getInfo($key, 'pev') . ' pev</em>';
							echo '</div>';
						echo '</div>';

						echo '<div id="sell-ships-' . $key . '" class="sell-form" data-shipcom-size="' . CommercialShipping::WEDGE . '" data-resource-rate="' . (ShipResource::getInfo($key, 'pev') * 1000) . '" data-max-quantity="' . $ship . '" data-rate="1" data-min-price="1" style="display:none;">';
							echo '<div class="label-box">';
								echo '<span class="label">Quantité max.</span>';
								echo '<span class="value">' . $ship . '</span>';
							echo '</div>';

							echo '<div class="label-box sf-quantity">';
								echo '<label for="sell-market-quantity-ship" class="label">Quantité</label>';
								echo '<input id="sell-market-quantity-ship" class="value val-quantity" type="text" name="quantity-' . $key . '" autocomplete="off" />';
							echo '</div>';	

							echo '<hr />';

							echo '<div class="label-box sf-comship">';
								echo '<span class="label">Vaisseaux</span>';
								echo '<span class="value"></span>';
								echo '<img class="icon-color" alt="vaisseaux transports" src="' . $mediaPath . 'resources/transport.png">';
							echo '</div>';

							echo '<hr />';

							echo '<p><input type="submit" name="identifier-' . $key . '" value="Envoyer" /></p>';
						echo '</div>';
					}
				}
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';
