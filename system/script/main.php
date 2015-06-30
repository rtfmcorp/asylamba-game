<?php
$scripts = [
	'Déploiment' => [
		['dbinstall', '/deploy/dbinstall.php'],
		['newgalaxy', '/deploy/newgalaxy.php'],
	],
	'Tâches Cron' => [
		['daily', '/cron/daily.php'],
		['playerranking', '/cron/playerRanking.php'],
		['factionranking', '/cron/factionRanking.php'],
	],
	'Utilitaires' => [
		['commanderAttack', '/utils/commanderAttack.php'],
		['sectors', '/utils/sectors.php'],
		['maprender', '/utils/map-render.php'],
		['findsectorinfos', '/utils/find-sector-infos.php'],
		['getstatistic', '/utils/get-stats.php'],
		['recolorsector', '/utils/recolor-sector.php'],
	],
	'Test' => [
		['main', '/test/test.php'],
	],
	'Migration' => [
		['color', '/migration/updateColor.php'],
		['recycling', '/migration/updateRecycling.php'],
		['sector', '/migration/updateSector.php'],
		['factionranking', '/migration/updateFactionRanking.php'],
		['addconversation', '/migration/add-conversation.php'],
		['removemessage', '/migration/remove-message.php'],
	]
];

# unlimited time
set_time_limit(0);
ini_set('display_errors', TRUE);

include SCRIPT . 'template/open.php';

if (DEVMODE || CTR::$get->equal('key', KEY_SCRIPT)) {
	if (!CTR::$get->exist('a')) {
		echo '<div class="list-script">';
			echo '<div class="return">';
				echo '<a href="' . APP_ROOT . 'buffer/key-' . KEY_BUFFER . '/">&#8801;</a> ';
				echo 'Liste des scripts';
			echo '</div>';

			echo '<div class="scripts">';
				foreach ($scripts as $type => $typeScripts) {
					echo '<h2>' . $type . '</h2>';

					foreach ($typeScripts as $i => $script) {
						echo '<a href="' . APP_ROOT . 'script/key-' . KEY_SCRIPT . '/a-' . $script[0] . '">';
							echo '<strong>' . $script[0] . '</strong>';
							echo $script[1];
						echo '</a>';
					}
				}
			echo '</div>';
		echo '</div>';
	} else {
		foreach ($scripts as $typeScripts) {
			foreach ($typeScripts as $i => $script) {
				if (CTR::$get->get('a') == $script[0]) {
					$scrp = SCRIPT . 'scripts' . $script[1];
					$name = $script[1];
				}
			}
		}

		echo '<div class="content-script">';
			echo '<div class="return">';
				echo '<a href="' . APP_ROOT . 'script/key-' . KEY_SCRIPT . '/">&#8801;</a> ';
				echo $name;
			echo '</div>';

			echo '<div class="script">';
				include $scrp;
			echo '</div>';
		echo '</div>';
	}
}

include SCRIPT . 'template/close.php';
?>