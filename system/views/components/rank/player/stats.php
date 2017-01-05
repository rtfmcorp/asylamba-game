<?php
# require

use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Zeus\Model\Player;

$playerManager = $this->getContainer()->get('zeus.player_manager');

echo '<div class="component player rank">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>A propos</h4>';

			echo '<div class="number-box">';
				echo '<span class="label">Joueurs actifs</span>';
				echo '<span class="value">' . Format::number($playerManager->count(array('statement' => Player::ACTIVE))) . '</span>';
				echo '<span class="group-link"><a href="#" title="compte tous les joueurs qui se sont connectés depuis 15 jours" class="hb lt">?</a></span>';
			echo '</div>';

			echo '<div class="number-box">';
				echo '<span class="label">Joueurs inscrits</span>';
				echo '<span class="value">' . Format::number($playerManager->count(array('statement' => array(Player::ACTIVE, Player::INACTIVE)))) . '</span>';
			echo '</div>';

			echo '<p>Le classement est mis à jour tous les jours à 3h (UTC+1) du matin.</p>';

			echo '<hr>';

			echo '<a class="more-button" href="' . APP_ROOT . 'rank/mode-top">Voir le haut du classement</a>';
		echo '</div>';
	echo '</div>';
echo '</div>';