<?php
# lastNotif componant
# in hermes package

# liste toutes les notifications de l'utilisateur

# require
	# [{notification}]	notification_lastNotif

echo '<div class="component notif">';
	echo '<div class="head">';
		echo '<h1>Notification</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tool">';
				echo '<span><a href="' . APP_ROOT . 'action/a-readallnotif">tout marquer comme lu</a></span>';
				echo '<span><a href="' . APP_ROOT . 'action/a-deleteallnotif" class="hb lt" title="tout supprimer">×</a></span>';
				echo '<span><a href="#" class="hb lt sh" data-target="info-notif" title="des infos">?</a></span>';
			echo '</div>';
			
			echo '<p class="info" id="info-notif" style="display:none;">';
				echo 'Les notifications sont les messages que vous recevez du gouvernement de votre ou vos planètes. Ces messages vous avertissent de 
				toutes les actions qui prennent fin dans le jeu, comme les constructions, les attaques et les développements technologiques par exemple. 
				Elles vous permettent d’avoir un compte rendu de toutes vos activités sur Asylamba.<br/>Au bout d\'un certain temps, elles seront automatiquement supprimées, sauf si vous les archivez.';
			echo '</p>'; 
			
			if (count($notification_lastNotif) > 0) {
				foreach ($notification_lastNotif as $n) {
					$readed = ($n->getReaded()) ? '' : 'unreaded';
					echo '<div class="notif ' . $readed . '" data-notif-id="' . $n->getId() . '">';
						echo '<h4 class="read-notif switch-class-parent" data-class="open">' . $n->getTitle() . '</h4>';
						echo '<div class="content">' . $n->getContent() . '</div>';
						echo '<div class="footer">';
							echo '<a href="' . APP_ROOT . 'action/a-archivenotif/id-' . $n->getId() . '">archiver</a> ou ';
							echo '<a href="' . APP_ROOT . 'action/a-deletenotif/id-' . $n->getId() . '">supprimer</a><br />';
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
?>