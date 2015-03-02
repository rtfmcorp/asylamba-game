<?php
$scripts = array(
	array('deploy.dbinstall', '/deploy/dbinstall.php'),
	array('deploy.newgalaxy', '/deploy/newgalaxy.php'),
	array('deploy.changecolor', '/deploy/changecolor.php'),

	array('migration.modifcolor', '/migration/modifColor.php'),
	array('migration.updatemessage', '/migration/updateMessage.php'),
	array('migration.addgaiaplayer', '/migration/addGaiaPlayer.php'),

	array('cron.daily', '/cron/daily.php'),
	array('cron.playerranking', '/cron/playerRanking.php'),
	array('cron.factionranking', '/cron/factionRanking.php'),

	array('test.dump', '/test/dump.php'),
	array('test.api', '/test/api.php'),
	array('test.ctc', '/test/ctc.php'),
	array('test.galaxy', '/test/galaxy.php'),
	array('test.updatetuto', '/test/updatetuto.php'),
	array('test.ctc', '/test/ctc.php'),
	array('test.virtualcommander', '/test/virtualcommander.php'),

	array('utils.sectors', '/utils/sectors.php'),
	array('utils.maprender', '/utils/map-render.php'),
	array('utils.findsectorinfos', '/utils/find-sector-infos.php'),
);

# unlimited time
set_time_limit(0);
ini_set('display_errors', TRUE);

include SCRIPT . 'template/open.php';

if (DEVMODE || CTR::$get->equal('key', KEY_SCRIPT)) {
	if (!CTR::$get->exist('a')) {
		echo '<div class="list-script">';
			echo '<h1>Liste des scripts</h1>';

			echo '<div class="scripts">';
			foreach ($scripts as $i => $script) {
				echo '<a href="' . APP_ROOT . 'script/key-' . KEY_SCRIPT . '/a-' . $script[0] . '">';
					echo '<strong>Lancer</strong>';
					echo $script[1];
				echo '</a>';
			}
			echo '</div>';
		echo '</div>';
	} else {
		foreach ($scripts as $i => $script) {
			if (CTR::$get->get('a') == $script[0]) {
				$scrp = SCRIPT . 'scripts' . $script[1];
				$name = $script[1];
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