<?php
echo '<div class="component">';
	echo '<div class="head skin-5">';
		echo '<h2>Paramètres du chat</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<a href="' . Format::actionBuilder('switchparams', ['params' => Params::REDIRECT_CHAT]) . '"" class="on-off-button ' . (Params::check(Params::REDIRECT_CHAT) ? NULL : 'disabled') . '">';
				echo 'Ouvrir le chat directement';
			echo '</a>';

			echo '<p>Ce paramètre permet, si il est activé, de rediriger automatiquement le bouton <em>chat</em> vers le serveur de <em>chat</em> externe.</p>';
			echo '<p>Dans le cas contraire, vous serez dirigé vers la page d\'explication du fonctionnement du chat.</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';