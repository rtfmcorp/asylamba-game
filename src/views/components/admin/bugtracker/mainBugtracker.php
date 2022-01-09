<?php
# ortograph component
# in appollon.admin.bugtracker package

# affichage du récapitulatif du bugtracker

# require
	# [BugTracker]	bugtracker
	# int 			waitingBugQuantity
	# int 			archivedBugQuantity
	# string 		listName

use App\Classes\Library\Format;

$container = $this->getContainer();
$appRoot = $container->getParameter('app_root');
$mediaPath = $container->getParameter('media');
$token = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class)->get('token');

echo '<div class="component size3 dock1 admin bugtracker">';
	echo '<div class="head skin-1">';
		echo '<img src="' . $mediaPath . 'alert/bug.png" alt="" />';
		echo '<h2>Rapports d\'erreur</h2>';
		echo '<em>' . $listName . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tool">';
				echo '<span><a href="' . $appRoot . 'admin/view-bugtracker">voir tous les rapports</a></span>';
				echo '<span><a href="' . $appRoot . 'admin/view-bugtracker/type-' . BugTracker::TYPE_BUG . '">Bug</a></span>';
				echo '<span><a href="' . $appRoot . 'admin/view-bugtracker/type-' . BugTracker::TYPE_ORTHOGRAPH . '">Orth</a></span>';
				echo '<span><a href="' . $appRoot . 'admin/view-bugtracker/type-' . BugTracker::TYPE_DISPLAY . '">Aff</a></span>';
				echo '<span><a href="' . $appRoot . 'admin/view-bugtracker/type-' . BugTracker::TYPE_CALIBRATION . '">Cal</a></span>';
				echo '<span><a href="' . $appRoot . 'admin/view-bugtracker/type-' . BugTracker::TYPE_IMPROVEMENT . '">Amé</a></span>';
			echo '</div>';

			echo '<h4>Rapports en attente de traitement</h4>';
			echo '<table>';
				echo '<tr>';
					echo '<td></td>';
					echo '<td>date</td>';
					echo '<td>message</td>';
					echo '<td>actions</td>';
				echo '</tr>';
				foreach ($bugtracker as $bug) {
					if ($bug->statement == BugTracker::ST_WAITING) {
						echo '<tr>';
							echo '<td><a href="' . $appRoot . 'embassy/player-' . $bug->rPlayer . '" class="button hb lt" title="voir le joueur qui a rapporter ce bug">' . $bug->rPlayer . '</td>';
							echo '<td>' . $bug->dSending . '</td>';
							echo '<td>' . $bug->url . '<br /><br />' . $bug->message . '</td>';
							echo '<td>';
								echo '<a href="' . Format::actionBuilder('archivebugreport', $token, ['id' => $bug->id]) . '" class="button hb lt" title="archiver">A</a>';
								echo ' <a href="' . Format::actionBuilder('deletebugreport', $token, ['id' => $bug->id]) . '" class="button hb lt" title="supprimer">X</a>';
							echo '</td>';
						echo '</tr>';
					}
				}				
			echo '</table>';

			echo '<h4>Rapports traités</h4>';
			echo '<table>';
				echo '<tr>';
					echo '<td></td>';
					echo '<td>date</td>';
					echo '<td>message</td>';
					echo '<td>actions</td>';
				echo '</tr>';
				foreach ($bugtracker as $bug) {
					if ($bug->statement == BugTracker::ST_ARCHIVED) {
						echo '<tr>';
							echo '<td><a href="' . $appRoot . 'embassy/player-' . $bug->rPlayer . '" class="button hb lt" title="voir le joueur qui a rapporter ce bug">' . $bug->rPlayer . '</td>';
							echo '<td>' . $bug->dSending . '</td>';
							echo '<td>' . $bug->url . '<br /><br />' . $bug->message . '</td>';
							echo '<td>';
								echo '<a href="' . Format::actionBuilder('deletebugreport', $token, ['id' => $bug->id]) . '" class="button hb lt" title="supprimer">X</a>';
							echo '</td>';
						echo '</tr>';
					}
				}
			echo '</table>';
		echo '</div>';
	echo '</div>';
echo '</div>';
