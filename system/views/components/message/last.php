<?php
# lastNotif componant
# in hermes package

# liste toutes les notifications de l'utilisateur

# require
	# [{notification}]	notification_lastNotif

$S_MSM_SCOPE = ASM::$msm->getCurrentSession();
ASM::$msm->changeSession($C_MSM1);

echo '<div class="component">';
	echo '<div class="head">';
		echo '<h1>Messagerie</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="set-item">';
				echo '<a class="' . (CTR::$get->equal('mode', 'create')  ? 'active' : NULL) . ' item" href="' . APP_ROOT . 'message/mode-create">';
					echo '<div class="left">';
						echo '<span>+</span>';
					echo '</div>';

					echo '<div class="center">';
						echo 'Démarrer une nouvelle conversation';
					echo '</div>';
				echo '</a>';

				if (count($threads) == 0) {
					echo '<p class="info">Vous n\'avez encore aucune conversation.</p>';
				} else {
					for ($i = 0; $i < count($threads); $i++) {
						$t = $threads[$i]['content'];
						
						$isNew = $threads[$i]['readed'] == FALSE
							? ' new'
							: NULL;

						echo '<div class="item ' . $isNew . '">';
							echo '<div class="left">';
								echo '<img ' . ($threads[$i]['readed'] == FALSE ? 'class="round-color' . $t->getRealColor(CTR::$data->get('playerId')) . '"' : NULL) . ' src="' . MEDIA . 'avatar/small/' . $t->getRealAvatar(CTR::$data->get('playerId')) . '.png" alt="">';
							echo '</div>';

							echo '<div class="center">';
								echo '<strong>' . $t->getRealName(CTR::$data->get('playerId')) . '</strong>';
								echo $threads[$i]['nb'] . ' message' . Format::plural($threads[$i]['nb']);
							echo '</div>';

							echo '<div class="right">';
								echo '<a class="' . (CTR::$get->equal('thread', $t->getThread())  ? 'active' : NULL) . '" href="' . APP_ROOT . 'message/thread-' . $t->getThread() . '"></a>';
							echo '</div>';
						echo '</div>';
					}
				}
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$msm->changeSession($S_MSM_SCOPE);
?>