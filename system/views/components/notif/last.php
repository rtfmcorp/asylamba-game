<?php
# lastNotif componant
# in hermes package

# liste toutes les notifications de l'utilisateur

# require
	# [{notification}]	notification_lastNotif

use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Library\Chronos;

$notificationManager = $this->getContainer()->get('hermes.notification_manager');
$sessionToken = $this->getContainer()->get('session_wrapper')->get('token');

$S_NTM_SCOPE = $notificationManager->getCurrentSession();
$notificationManager->changeSession($C_NTM1);

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>Notifications</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tool">';
				echo '<span><a href="' . Format::actionBuilder('readallnotif', $sessionToken) . '">tout marquer comme lu</a></span>';
				echo '<span><a href="' . Format::actionBuilder('deleteallnotif', $sessionToken) . '" class="hb lt" title="tout supprimer">×</a></span>';
				echo '<span><a href="#" class="hb lt sh" data-target="info-notif" title="plus d\'information">?</a></span>';
			echo '</div>';
			
			echo '<p class="info" id="info-notif" style="display:none;">';
				echo 'Les notifications sont les messages que vous recevez du gouvernement de votre ou vos planètes. Ces messages vous avertissent de 
				toutes les actions qui prennent fin dans le jeu, comme les attaques et les développements technologiques par exemple. 
				Elles vous permettent d’avoir un compte rendu de toutes vos activités sur Asylamba.<br/>Au bout d\'un certain temps, elles seront automatiquement supprimées, sauf si vous les archivez.';
			echo '</p>'; 
			
			if ($notificationManager->size() > 0) {
				for ($i = 0; $i < $notificationManager->size(); $i++) {
					$n = $notificationManager->get($i);

					$readed = ($n->getReaded()) ? '' : 'unreaded';
					echo '<div class="notif ' . $readed . '" data-notif-id="' . $n->getId() . '">';
						echo '<h4 class="read-notif switch-class-parent" data-class="open">' . $n->getTitle() . '</h4>';
						echo '<div class="content">' . $n->getContent() . '</div>';
						echo '<div class="footer">';
							echo '<a href="' . Format::actionBuilder('archivenotif', $sessionToken, ['id' => $n->getId()]) . '">archiver</a> ou ';
							echo '<a href="' . Format::actionBuilder('deletenotif', $sessionToken, ['id' => $n->getId()]) . '">supprimer</a><br />';
							echo '— ' . Chronos::transform($n->getDSending());
						echo '</div>';
					echo '</div>';
				}
			} else {
				echo '<p>Il n\'y a aucune notification dans votre boîte de réception.</p>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

$notificationManager->changeSession($S_NTM_SCOPE);
