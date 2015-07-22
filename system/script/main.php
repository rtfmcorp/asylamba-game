<?php
$scripts = [
	'Déploiment' => [
		['deploy.dbinstall', '/deploy/dbinstall.php'],
		['deploy.newgalaxy', '/deploy/newgalaxy.php'],
	],
	'Tâches Cron' => [
		['cron.daily', '/cron/daily.php'],
		['cron.playerranking', '/cron/playerRanking.php'],
		['cron.factionranking', '/cron/factionRanking.php'],
	],
	'Utilitaires' => [
		['utils.commanderAttack', '/utils/commanderAttack.php'],
		['utils.sectors', '/utils/sectors.php'],
		['utils.maprender', '/utils/map-render.php'],
		['utils.findsectorinfos', '/utils/find-sector-infos.php'],
		['utils.getstatistic', '/utils/get-stats.php'],
		['utils.recolorsector', '/utils/recolor-sector.php'],
	],
	'Test' => [
		['test.main', '/test/test.php'],
		['test.http', '/test/http-data.php'],
	],
	'Migration' => [
		['migration.color', '/migration/updateColor.php'],
		['migration.recycling', '/migration/updateRecycling.php'],
		['migration.sector', '/migration/updateSector.php'],
		['migration.factionranking', '/migration/updateFactionRanking.php'],
		['migration.addconversation', '/migration/add-conversation.php'],
		['migration.removemessage', '/migration/remove-message.php'],
	]
];

# unlimited time
set_time_limit(250);
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