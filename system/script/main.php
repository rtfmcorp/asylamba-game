<?php

$scripts = [
	'Déploiment' => [
		['deploy_dbinstall', '/deploy/dbinstall.php'],
		['deploy_newgalaxy', '/deploy/newgalaxy.php'],
	],
	'Serveur' => [
		['monitoring', '/server/monitoring.php']
	],
	'Tâches Cron' => [
		['cron_daily', '/cron/daily.php'],
		['cron_playerranking', '/cron/playerRanking.php'],
		['cron_factionranking', '/cron/factionRanking.php'],
	],
	'Utilitaires' => [
		['utils_commanderAttack', '/utils/commanderAttack.php'],
		['utils_sectors', '/utils/sectors.php'],
		['utils_maprender', '/utils/map-render.php'],
		['utils_findsectorinfos', '/utils/find-sector-infos.php'],
		['utils_getstatistic', '/utils/get-stats.php'],
		['utils_recolorsector', '/utils/recolor-sector.php'],
	],
	'Test' => [
		['test_main', '/test/test.php'],
		['test_http', '/test/http-data.php'],
		['test_updateSenatAphera', '/test/updateSenate.php'],
	],
	'Migration' => [
		['migration_color', '/migration/updateColor.php'],
		['migration_recycling', '/migration/updateRecycling.php'],
		['migration_sector', '/migration/updateSector.php'],
		['migration_factionranking', '/migration/updateFactionRanking.php'],
		['migration_addconversation', '/migration/add-conversation.php'],
		['migration_removemessage', '/migration/remove-message.php'],
	]
];

# unlimited time
set_time_limit(250);
ini_set('display_errors', TRUE);

include SCRIPT . 'template/open.php';

$request = $this->getContainer()->get('app.request');
$scriptKey = $this->getContainer()->getParameter('security_script_key');

if ($this->getContainer()->getParameter('environment') === 'dev' || $request->query->get('key') === $scriptKey) {
	if (!$request->query->has('a')) {
		echo '<div class="list-script">';
			echo '<div class="return">';
				echo '<a href="' . APP_ROOT . 'buffer/key-' . $this->getContainer()->getParameter('security_buffer_key') . '/">&#8801;</a> ';
				echo 'Liste des scripts';
			echo '</div>';

			echo '<div class="scripts">';
				foreach ($scripts as $type => $typeScripts) {
					echo '<h2>' . $type . '</h2>';

					foreach ($typeScripts as $i => $script) {
						echo '<a href="' . APP_ROOT . 'script/key-' . $scriptKey . '/a-' . $script[0] . '">';
							echo '<strong>' . $script[0] . '</strong>';
							echo $script[1];
						echo '</a>';
					}
				}
			echo '</div>';
		echo '</div>';
	} else {
		$requestedScript = $request->query->get('a');
		foreach ($scripts as $typeScripts) {
			foreach ($typeScripts as $i => $script) {
				if ($requestedScript === $script[0]) {
					$scrp = SCRIPT . 'scripts' . $script[1];
					$name = $script[1];
				}
			}
		}

		echo '<div class="content-script">';
			echo '<div class="return">';
				echo '<a href="' . APP_ROOT . 'script/key-' . $scriptKey . '/">&#8801;</a> ';
				echo $name;
			echo '</div>';

			echo '<div class="script">';
				include $scrp;
			echo '</div>';
		echo '</div>';
	}
}

include SCRIPT . 'template/close.php';