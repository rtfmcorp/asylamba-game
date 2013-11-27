<?php
# ortograph component
# in appollon.admin.bugtracker package

# affichage du récapitulatif du bugtracker

# require
	# [BugTracker]	bugtracker
	# int 			waitingBugQuantity
	# int 			archivedBugQuantity
	# string 		listName


echo '<div class="component size3 dock1 admin bugtracker">';
	echo '<div class="head skin-1">';
		echo '<img src="' . MEDIA . 'alert/bug.png" alt="" />';
		echo '<h2>Rapports d\'erreur</h2>';
		echo '<em>' . $listName . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="tool">';
				echo '<span><a href="' . APP_ROOT . 'admin/view-bugtracker">voir tous les rapports</a></span>';
				echo '<span><a href="' . APP_ROOT . 'admin/view-bugtracker/type-' . BugTracker::TYPE_BUG . '">Bug</a></span>';
				echo '<span><a href="' . APP_ROOT . 'admin/view-bugtracker/type-' . BugTracker::TYPE_ORTHOGRAPH . '">Orth</a></span>';
				echo '<span><a href="' . APP_ROOT . 'admin/view-bugtracker/type-' . BugTracker::TYPE_DISPLAY . '">Aff</a></span>';
				echo '<span><a href="' . APP_ROOT . 'admin/view-bugtracker/type-' . BugTracker::TYPE_CALIBRATION . '">Cal</a></span>';
				echo '<span><a href="' . APP_ROOT . 'admin/view-bugtracker/type-' . BugTracker::TYPE_IMPROVEMENT . '">Amé</a></span>';
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
							echo '<td><a href="' . APP_ROOT . 'diary/player-' . $bug->rPlayer . '" class="button hb lt" title="voir le joueur qui a rapporter ce bug">' . $bug->rPlayer . '</td>';
							echo '<td>' . $bug->dSending . '</td>';
							echo '<td>' . $bug->url . '<br /><br />' . $bug->message . '</td>';
							echo '<td>';
								echo '<a href="' . APP_ROOT . 'action/a-archivebugreport/id-' . $bug->id . '" class="button hb lt" title="archiver">A</a>';
								echo ' <a href="' . APP_ROOT . 'action/a-deletebugreport/id-' . $bug->id . '" class="button hb lt" title="supprimer">X</a>';
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
							echo '<td><a href="' . APP_ROOT . 'diary/player-' . $bug->rPlayer . '" class="button hb lt" title="voir le joueur qui a rapporter ce bug">' . $bug->rPlayer . '</td>';
							echo '<td>' . $bug->dSending . '</td>';
							echo '<td>' . $bug->url . '<br /><br />' . $bug->message . '</td>';
							echo '<td>';
								echo '<a href="' . APP_ROOT . 'action/a-deletebugreport/id-' . $bug->id . '" class="button hb lt" title="supprimer">X</a>';
							echo '</td>';
						echo '</tr>';
					}
				}
			echo '</table>';
		echo '</div>';
	echo '</div>';
echo '</div>';
/*
echo '<div class="component nav">';
	echo '<div class="head">';
		echo '<h1>Bugtracker</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="number-box">';
				echo '<span class="label">bla</span>';
				echo '<span class="value">' . Format::numberFormat(0) . '</span>';
			echo '</div>';

			echo '<hr />';

			echo '<div class="number-box">';
				echo '<span class="label">Erreurs en attente</span>';
				echo '<span class="value">' . Format::numberFormat($waitingBug_mainBugtracker) . '</span>';
			echo '</div>';
			echo '<div class="number-box">';
				echo '<span class="label">Erreurs corrigées</span>';
				echo '<span class="value">' . Format::numberFormat($archivedBug_mainBugtracker) . '</span>';
			echo '</div>';

			echo '<hr />';

			echo '<div class="number-box">';
				echo '<span class="label">Bugs & erreurs</span>';
				echo '<span class="value">' . Format::numberFormat(count($bugtracker_error)) . '</span>';
			echo '</div>';
			echo '<div class="number-box">';
				echo '<span class="label">Fautes d\'orthographe</span>';
				echo '<span class="value">' . Format::numberFormat(count($bugtracker_orthograph)) . '</span>';
			echo '</div>';
			echo '<div class="number-box">';
				echo '<span class="label">Boite à idées</span>';
				echo '<span class="value">' . Format::numberFormat(count($bugtracker_improvement)) . '</span>';
			echo '</div>';
			echo '<div class="number-box">';
				echo '<span class="label">Erreurs d\'affichage</span>';
				echo '<span class="value">' . Format::numberFormat(count($bugtracker_display)) . '</span>';
			echo '</div>';
			echo '<div class="number-box">';
				echo '<span class="label">Erreurs diverses</span>';
				echo '<span class="value">' . Format::numberFormat(count($bugtracker_bug)) . '</span>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';*/