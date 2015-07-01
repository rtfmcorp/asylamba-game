<?php
# display
echo '<div class="component">';
	echo '<div class="head skin-5">';
		echo '<h2>Parrainage</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Parrainez vos amis !</h4>';
			echo '<p>Vous avez la possibilité de parrainer vos amis. Pour le faire, deux solutions. Vous pouvez soit envoyer le lien ci-dessous à vos amis, soit entrer leur adresse e-mail et nous leur enverrons un e-mail.</p>';
			echo '<p>Envoyez le lien suivant à vos amis :</p>';
			$sponsorLink = GETOUT_ROOT . 'action/a-invitation/i-' . CTR::$data->get('playerId') . '/s-' . APP_ID;
			//echo '<p><input type="text" name="sponsor-link" value="' . $sponsorLink . '""></p>';
			echo '<p><textarea name="textarea" rows="2">' . $sponsorLink . '</textarea></p>';
			echo '<form action="' . Format::actionBuilder('sendsponsorshipemail') . '" method="post">';
				echo '<p>Entrez l\'adresse e-mail de votre ami :</p>';
				echo '<p><input type="email" name="email" placeholder="e-mail" required></p>';
				echo '<p><input type="submit" value="Envoyer l\'e-mail d\'invitation"></p>';
			echo '</form>';

			echo '<h4>Avantages</h4>';
			echo '<p>Pour chacun de vos amis qui crée un compte, vous recevrez 1000 crédits. De plus, lorsqu\'il attendra le niveau 3, vous recevrez <b>1\'000\'000 crédits</b> ! Et cela est valable pour toutes les personnes que vous parrainez. Propagez donc le jeu autour de vous, à vos amis, à vos familles, sur Facebook, etc.</p>';

			echo '<h4>Conditions</h4>';
			echo '<p>Attention toutefois, ces gains pourraient vous donner envie de créer de faux comptes. Si nous remarquons que vous possédez plusieurs comptes, ils seront <b>tous</b> supprimés sans préavis. Soyez donc fair-play.</p>';

			/*echo '<h4>Concours</h4>';
			echo '<p>De plus un concours est mis en place pour gagner de gros prix, à savoir des vaisseaux (Phenix et autre). Le x x 201x, nous compterons les points de parrainage de tout le monde. Un filleul niveau 1 rapporte 1 point, un filleul niveau 2 rapporte 2 points, etc. Les joueurs du top 3 se verront recevoir des prix.</p>';*/
			echo '<h4>Liste de vos filleuls</h4>';
			$qr = $db->prepare('SELECT 
					p.id AS id,
					p.name AS name,
					p.level AS level
				FROM player AS p 
				WHERE p.rGodFather = ?');
			$qr->execute(array(CTR::$data->get('playerId')));

			while ($aw = $qr->fetch()) {
				echo '<p><a href="' . APP_ROOT . 'embassy/player-' . $aw['id'] . '">' . $aw['name'] . '</a> : niveau ' . $aw['level'] . '</p>';
			}
			echo '<br/><br/>';
		echo '</div>';
	echo '</div>';
echo '</div>';

