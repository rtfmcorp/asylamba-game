<?php
# archivedNotif componant
# in hermes package

# liste toutes les notifications archivées de l'utilisateur

# require
	# [{notification}]	notification_archivedNotif

echo '<div class="component notif">';
	echo '<div class="head">';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h3>Archive des notifications</h3>';

			foreach ($notification_archivedNotif as $n) {
				$readed = ($n->getReaded()) ? '' : 'unreaded';
				echo '<div class="notif ' . $readed . '">';
					echo '<h4 class="switch-class-parent" data-class="open">' . $n->getTitle() . '</h4>';
					echo '<div class="content">' . $n->getContent() . '</div>';
					echo '<div class="footer">';
						echo '<a href="' . APP_ROOT . 'action/a-archivenotif/id-' . $n->getId() . '">désarchiver</a> ou ';
						echo '<a href="' . APP_ROOT . 'action/a-deletenotif/id-' . $n->getId() . '">supprimer</a><br />';
						echo '— ' . Chronos::transform($n->getDSending());
					echo '</div>';
				echo '</div>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';
?>