<?php
include_once GAIA;
include_once ZEUS;

# background paralax
echo '<div id="background-paralax" class="profil"></div>';

# inclusion des elements
include 'inscriptionElement/movers.php';
include 'inscriptionElement/subnav.php';

# contenu spécifique
echo '<div id="content">';
	echo '<form action="' . APP_ROOT . 'inscription/step-4" method="post" >';
		echo '<div class="component inscription color' . CTR::$data->get('inscription')->get('ally') . '">';
			echo '<div class="head">';
				echo '<h1>Localisation</h1>';
			echo '</div>';
			echo '<div class="fix-body">';
				echo '<div class="body">';
					echo '<p class="info">Remplissez maintenant les informations de votre première base.</p>';
					echo '<hr />';
					echo '<p><input type="text" name="base" id="base" maxlength="20" required placeholder="nom de votre planète" /></p>';
					echo '<p class="info">Vous pourrez changer ce nom plus tard.</p>';
				echo '</div>';
			echo '</div>';
		echo '</div>';

		echo '<div class="component inscription size2 color' . CTR::$data->get('inscription')->get('ally') . '">';
			echo '<div class="head skin-2">';
				echo '<h2>Choisissez dans quel secteur vous souhaitez être</h2>';
			echo '</div>';
			echo '<div class="fix-body">';
				echo '<div class="body">';
					echo '<p class="info">Ceci sera par la suite une carte interactive, mais pour l\'instant faites au pif.</p>';
					$db = Database::getInstance();
					$qr = $db->query('SELECT id FROM sector'); // pour pouvoir choisir n'importe quel secteur
					# $qr = $db->query('SELECT id FROM sector WHERE rColor = ' . CTR::$data->get('inscription')->get('ally'));
					$aw = $qr->fetchAll();
					foreach ($aw as $v) {
						echo '<p><input type="radio" name="sector" value="' . $v['id'] . '" id="sector' . $v['id'] . '" required />';
						echo '<label for="sector' . $v['id'] . '">';
							echo 'secteur #' . $v['id'];
						echo '</label></p>';
					}
				echo '</div>';
			echo '</div>';
		echo '</div>';

		echo '<div class="component inscription color' . CTR::$data->get('inscription')->get('ally') . '">';
			echo '<div class="head">';
			echo '</div>';
			echo '<div class="fix-body">';
				echo '<div class="body">';
					echo '<p><input type="submit" id="nextStep" value="valider et commencer le jeu" /></p>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</form>';
echo '</div>';
?>