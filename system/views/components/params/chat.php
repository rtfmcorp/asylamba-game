<?php
echo '<div class="component new-message">';
	echo '<div class="head skin-5">';
		echo '<h2>Paramètres du chat</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<a href="' . Format::actionBuilder('switchparams', ['params' => Params::REDIRECT_CHAT]) . '" class="on-off-button ' . (Params::check(Params::REDIRECT_CHAT) ? NULL : 'disabled') . '">';
				echo 'Ouvrir le chat directement';
			echo '</a>';

			echo '<p>Ce paramètre permet, si il est activé, de rediriger automatiquement le bouton <em>chat</em> vers le serveur de <em>chat</em> externe.</p>';
			echo '<p>Dans le cas contraire, vous serez dirigé vers la page d\'explication du fonctionnement du chat.</p>';

			echo '<hr />';

			echo '<h4>Utilisation et activation du chat</h4>';
			echo '<p>EXPLICATION DISCORD ! Ce paramètre permet, si il est activé, de rediriger automatiquement le bouton <em>chat</em> vers le serveur de <em>chat</em> externe.</p>';
			echo '<p>Premièrement, créez un compte sur discorde en suivant ce lien :</p>';
			echo '<a href="https://discordapp.com/register" target="_blank" class="on-off-button">';
				echo 'Créer un compte sur discord';
			echo '</a>';
			
			echo '<p>Récup de votre ID</p>';
			echo '<p>Montrer quelle commande noter</p>';
			
			echo '<h4>S\'inscire sur Asylamba@Discord</h4>';
			echo '<form action="' . Format::actionBuilder('tmp', []) . '" method="post">';
				echo '<p class="input input-text">';
					echo '<input name="discord-id" type="text" placeholder="votre ID discord" />';
				echo '</p>';
				echo '<p><button type="submit">Se connecter</button></p>';
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';