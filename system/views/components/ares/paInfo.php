<?php
# paInfo component
# in athena package

# affiche l'aperçu des points d'attaque d'un joueur

# require
	# NULL

echo '<div class="component financial">';
	echo '<div class="head skin-2">';
		echo '<h2>Gestion des PA</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			/*echo '<div class="tool">';
				echo '<span><a href="#" class="hb lt" title="a faire">acheter 15 PA pour 50 000 crédits</a></span>';
			echo '</div>';*/

			echo '<div class="number-box">';
				echo '<span class="label">PA bruts</span>';
				echo '<span class="value">';
					echo ((CTR::$data->get('playerInfo')->get('level') * PAM_COEFFAP) + PAM_BASEAP);
					echo ' <img src="' . MEDIA . 'resources/pa.png" alt="pa" class="icon-color" />';
				echo '</span>';
			echo '</div>';
			echo '<div class="number-box">';
				echo '<span class="label">PA à disposition</span>';
				echo '<span class="value">';
					echo CTR::$data->get('playerInfo')->get('actionPoint');
					echo ' <img src="' . MEDIA . 'resources/pa.png" alt="pa" class="icon-color" />';
				echo '</span>';
			echo '</div>';
			echo '<div class="number-box">';
				echo '<span class="label">PA à la prochaine relève</span>';
				echo '<span class="value">';
					echo (((CTR::$data->get('playerInfo')->get('level') * PAM_COEFFAP) + PAM_BASEAP) + ceil(CTR::$data->get('playerInfo')->get('actionPoint') / 2));
					echo ' <img src="' . MEDIA . 'resources/pa.png" alt="pa" class="icon-color" />';
				echo '</span>';
			echo '</div>';

			echo '<p class="info">A chaque relève, vos points d\'attaque sont élevés à leur valeur brute. Cette dernière est égale à ' . PAM_BASEAP . ' additionné à ' . PAM_COEFFAP . ' fois votre niveau. De plus, un bonus égal à la moitié de vos points d\'attaque inutilisé à la fin de la relève précédente est ajouté.</p>';
			echo '<p class="info">Certaines attaques nécéssitant plus du maximum de points (points d\'attaque brut), il est intéressant d\'économiser un grand nombre de PA afin d\'augmenter la valeur du bonus.</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';