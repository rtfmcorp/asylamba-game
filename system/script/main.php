<?php
$scripts = array(
	array('deploy.dbinstall', '/deploy/dbinstall.php'),
	array('deploy.newgalaxy', '/deploy/newgalaxy.php'),
	array('deploy.changecolor', '/deploy/changecolor.php'),
	array('test.dump', '/test/dump.php'),
	array('test.api', '/test/api.php'),
	array('test.ctc', '/test/ctc.php'),
	array('test.galaxy', '/test/galaxy.php'),
	array('database.addbugtracker', '/database/addbugtracker.php'),
	array('database.addtransaction', '/database/addtransaction.php'),
	array('database.addcommercialshipping', '/database/addcommercialshipping.php'),
	array('database.addcommercialtax', '/database/addcommercialtax.php'),
	array('database.addorbitalbase', '/database/addorbitalbase.php'),
	array('database.addorbitalbasebuildingqueue', '/database/addorbitalbasebuildingqueue.php'),
	array('database.addorbitalbaseshipqueue', '/database/addorbitalbaseshipqueue.php'),
	array('database.addtechnologyqueue', '/database/addtechnologyqueue.php'),
	array('database.addplayer', '/database/addplayer.php'),
	array('database.addcolor', '/database/addcolor.php'),
	array('database.addcommander', '/database/addCommander.php'),
	array('database.addreport', '/database/addReport.php'),
	array('database.addspyreport', '/database/addspyreport.php'),
	array('database.removedescriptionfromplayer', '/database/removeDescriptionFromPlayer.php'),
	array('database.addstepdoneinplayer', '/database/addStepDoneInPlayer.php'),
	array('database.addrankings', '/database/addRankings.php'),
	array('database.addfaction', '/database/addFaction.php'),
	array('database.updateplayer', '/database/updatePlayer.php'),
	array('cron.daily', '/cron/daily.php'),
	array('cron.playerranking', '/cron/playerRanking.php'),
	array('cron.factionranking', '/cron/factionRanking.php')
);

# unlimited time
set_time_limit(0);
ini_set('display_errors', TRUE);

include SCRIPT . 'template/open.php';

if (DEVMODE || CTR::$get->equal('password', PWD_SCRIPT)) {
	if (!CTR::$get->exist('a')) {
		echo '<div class="list-script">';
			echo '<h1>Liste des scripts</h1>';

			foreach ($scripts as $i => $script) {
				echo '<a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/a-' . $script[0] . '">' . $script[1] . '</a>';
			}
		echo '</div>';
	} else {
		echo '<div class="content-script">';
			echo '<div class="return"><a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/">revenir vers les scripts</a></div>';

			foreach ($scripts as $i => $script) {
				if (CTR::$get->get('a') == $script[0]) {
					include SCRIPT . 'scripts' . $script[1]; break;
				}
			}
		echo '</div>';
	}
}

include SCRIPT . 'template/close.php';
?>