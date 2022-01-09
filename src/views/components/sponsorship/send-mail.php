<?php

use App\Classes\Library\Format;

$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$sessionToken = $session->get('token');

# display
echo '<div class="component new-message">';
	echo '<div class="head skin-2"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Propagez votre lien</h4>';

			echo '<p>Vous pouvez coller ce lien sur vos profils de vos réseaux sociaux ou les partager, toute personne qui s\'inscrira à Asylamba après avoir cliqué sur ce lien sera considérée comme votre filleul.</p>';

			echo '<p>Copiez ce lien</p>';
			echo '<p class="input input-area">';
				echo '<textarea style="height: 60px;">' . ($this->getContainer()->getParameter('getout_root') . 'action/a-invitation/i-' . $session->get('playerId') . '/s-' . $this->getContainer()->getParameter('server_id')) . '</textarea>';
			echo '</p>';


			echo '<h4>Envoyez un email</h4>';
		
			echo '<form action="' . Format::actionBuilder('sendsponsorshipemail', $sessionToken) . '" method="post">';
				echo '<p>Adresse e-mail du filleul</p>';
				echo '<p class="input input-text">';
					echo '<input type="email" name="email" placeholder="e-mail" required />';
				echo '</p>';
				echo '<p><button type="submit">Envoyer l\'e-mail d\'invitation</button></p>';
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';

