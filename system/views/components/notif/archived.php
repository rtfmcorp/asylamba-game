<?php
# archivedNotif componant
# in hermes package

# liste toutes les notifications archivées de l'utilisateur

# require
	# [{notification}]	notification_archivedNotif

use Asylamba\Classes\Worker\ASM;
use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Library\Chronos;

$S_NTM_SCOPE = ASM::$ntm->getCurrentSession();
ASM::$ntm->changeSession($C_NTM2);

echo '<div class="component">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h3>Archive des notifications</h3>';

			for ($i = 0; $i < ASM::$ntm->size(); $i++) {
				$n = ASM::$ntm->get($i);

				$readed = ($n->getReaded()) ? '' : 'unreaded';
				echo '<div class="notif ' . $readed . '" data-notif-id="' . $n->getId() . '">';
					echo '<h4 class="read-notif switch-class-parent" data-class="open">' . $n->getTitle() . '</h4>';
					echo '<div class="content">' . $n->getContent() . '</div>';
					echo '<div class="footer">';
						echo '<a href="' . Format::actionBuilder('archivenotif', ['id' => $n->getId()]) . '">archiver</a> ou ';
						echo '<a href="' . Format::actionBuilder('deletenotif', ['id' => $n->getId()]) . '">supprimer</a><br />';
						echo '— ' . Chronos::transform($n->getDSending());
					echo '</div>';
				echo '</div>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$ntm->changeSession($S_NTM_SCOPE);
