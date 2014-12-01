<?php
echo '<div class="component new-message market-sell">';
	echo '<div class="head skin-4 sh">';
		echo '<img src="' . MEDIA . 'resources/resource.png" alt="ressource" class="main" />';
		echo '<h2>Envoi de ressources</h2>';
		echo '<em>à une autre base</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<form action="' . APP_ROOT . 'action/a-giveresource/baseid-' . $ob_compPlat->rPlace . '" method="post">';
				# base
				echo '<p><label for="send-resources-target">Base destinataire</label></p>';
				echo '<input class="autocomplete-hidden" type="hidden" name="otherbaseid" />';
				echo '<p class="input input-text"><input type="text" id="send-resources-target" class="autocomplete-orbitalbase" name="name" autocomplete="off" /></p>';

				# form de vente
				echo '<div class="sell-form" data-shipcom-size="' . CommercialShipping::WEDGE . '" data-resource-rate="1" data-max-quantity="' . $ob_compPlat->resourcesStorage . '" data-rate="0" data-min-price="0">';
					echo '<div class="label-box">';
						echo '<span class="label">Ressources</span>';
						echo '<span class="value">' . Format::numberFormat($ob_compPlat->resourcesStorage) . '</span>';
						echo '<img class="icon-color" alt="ressources" src="' . MEDIA . 'resources/resource.png">';
					echo '</div>';

					echo '<div class="label-box sf-quantity">';
						echo '<label for="send-resources-quantity" class="label">Quantité</label>';
						echo '<input id="send-resources-quantity" class="value" type="text" name="quantity" autocomplete="off" />';
						echo '<img class="icon-color" alt="ressources" src="' . MEDIA . 'resources/resource.png">';
					echo '</div>';

					echo '<hr />';

					echo '<div class="label-box sf-comship">';
						echo '<span class="label">Vaisseaux</span>';
						echo '<span class="value"></span>';
						echo '<img class="icon-color" alt="vaisseaux transports" src="' . MEDIA . 'resources/transport.png">';
					echo '</div>';

					echo '<hr />';

					echo '<p><input type="submit" value="Envoyer" /></p>';
				echo '</div>';
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>