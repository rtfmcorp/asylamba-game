<?php
# archivedNotif componant
# in hermes package

# liste toutes les notifications archivées de l'utilisateur

# require
	# [{notification}]	notification_archivedNotif

use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Library\Chronos;

$sessionToken = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class)->get('token');

echo '<div class="component">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h3>Archive des notifications</h3>';

			foreach ($archivedNotifications as $n) {
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
		echo '</div>';
	echo '</div>';
echo '</div>';
