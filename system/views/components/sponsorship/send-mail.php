<?php
# display
echo '<div class="component new-message">';
	echo '<div class="head skin-2"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Propagez votre lien</h4>';

			echo '<p>Vous pouvez coller ce lien sur vos profil de vos réseaux sociaux ou les partagers, toutes personnes qui s\'inscrira à Asylamba après avoir cliqué sur ce lien sera considérée comme votre filleul.</p>';

			echo '<p>Copiez ce lien</p>';
			echo '<p class="input input-area">';
				echo '<textarea style="height: 60px;">' . (GETOUT_ROOT . 'action/a-invitation/i-' . CTR::$data->get('playerId') . '/s-' . APP_ID) . '</textarea>';
			echo '</p>';


			echo '<h4>Envoyez un email</h4>';
		
			echo '<form action="' . Format::actionBuilder('sendsponsorshipemail') . '" method="post">';
				echo '<p>Adresse e-mail du filleul</p>';
				echo '<p class="input input-text">';
					echo '<input type="email" name="email" placeholder="e-mail" required />';
				echo '</p>';
				echo '<p><button type="submit">Envoyer l\'e-mail d\'invitation</button></p>';
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';

