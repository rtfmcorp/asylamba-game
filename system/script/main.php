<?php
# unlimited time
set_time_limit(0);
ini_set('display_errors', TRUE);

include SCRIPT . 'template/open.php';

if (DEVMODE || CTR::$get->equal('password', PWD_SCRIPT)) {
	if (!CTR::$get->exist('a')) {
		echo '<div class="list-script">';
			echo '<h1>Liste des scripts</h1>';
			echo '<a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/a-deploy.dbinstall">/deploy/dbinstall.php</a>';
			echo '<a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/a-deploy.newgalaxy">/deploy/newgalaxy.php</a>';
			echo '<a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/a-deploy.changecolor">/deploy/changecolor.php</a>';
			echo '<a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/a-test.dump">/test/dump.php</a>';
			echo '<a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/a-test.api">/test/api.php</a>';
			echo '<a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/a-test.ctc">/test/ctc.php</a>';
			echo '<a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/a-test.galaxy">/test/galaxy.php</a>';
			echo '<a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/a-database.addbugtracker">/database/addbugtracker.php</a>';
			echo '<a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/a-database.addtransaction">/database/addtransaction.php</a>';
			echo '<a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/a-database.addcommercialshipping">/database/addcommercialshipping.php</a>';
			echo '<a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/a-database.addcommercialtax">/database/addcommercialtax.php</a>';
			echo '<a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/a-database.addorbitalbase">/database/addorbitalbase.php</a>';
			echo '<a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/a-database.addorbitalbasebuildingqueue">/database/addorbitalbasebuildingqueue.php</a>';
			echo '<a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/a-database.addorbitalbaseshipqueue">/database/addorbitalbaseshipqueue.php</a>';
			echo '<a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/a-database.addtechnologyqueue">/database/addtechnologyqueue.php</a>';
			echo '<a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/a-database.addplayer">/database/addplayer.php</a>';
			echo '<a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/a-database.addcolor">/database/addcolor.php</a>';
			echo '<a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/a-database.addcommander">/database/addCommander.php</a>';
			echo '<a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/a-database.addreport">/database/addReport.php</a>';
			echo '<a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/a-database.addspyreport">/database/addspyreport.php</a>';
			echo '<a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/a-database.removedescriptionfromplayer">/database/removeDescriptionFromPlayer.php</a>';
			echo '<a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/a-database.addstepdoneinplayer">/database/addStepDoneInPlayer.php</a>';
			echo '<a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/a-database.addrankings">/database/addRankings.php</a>';
			echo '<a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/a-database.addfaction">/database/addFaction.php</a>';
			echo '<a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/a-database.updateplayer">/database/updatePlayer.php</a>';
			echo '<a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/a-cron.daily">/cron/daily.php</a>';
			echo '<a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/a-cron.playerranking">/cron/playerRanking.php</a>';
			echo '<a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/a-cron.factionranking">/cron/factionRanking.php</a>';
		echo '</div>';
	} else {
		echo '<div class="content-script">';
			echo '<div class="return"><a href="' . APP_ROOT . 'script/password-' . PWD_SCRIPT . '/">revenir vers les scripts</a></div>';
			switch (CTR::$get->get('a')) {
				case 'deploy.dbinstall': 					include SCRIPT . 'scripts/deploy/dbinstall.php'; break;
				case 'deploy.newgalaxy': 					include SCRIPT . 'scripts/deploy/newgalaxy.php'; break;
				case 'deploy.changecolor': 					include SCRIPT . 'scripts/deploy/changecolor.php'; break;

				case 'test.dump':							include SCRIPT . 'scripts/test/dump.php'; break;
				case 'test.api':							include SCRIPT . 'scripts/test/api.php'; break;
				case 'test.ctc':							include SCRIPT . 'scripts/test/ctc.php'; break;
				case 'test.galaxy': 						include SCRIPT . 'scripts/test/galaxy.php'; break;

				case 'database.addbugtracker':				include SCRIPT . 'scripts/database/addbugtracker.php'; break;
				case 'database.addtransaction':				include SCRIPT . 'scripts/database/addtransaction.php'; break;
				case 'database.addcommercialshipping': 		include SCRIPT . 'scripts/database/addcommercialshipping.php'; break;
				case 'database.addcommercialtax':			include SCRIPT . 'scripts/database/addcommercialtax.php'; break;
				case 'database.addorbitalbase':				include SCRIPT . 'scripts/database/addorbitalbase.php'; break;
				case 'database.addorbitalbasebuildingqueue':include SCRIPT . 'scripts/database/addorbitalbasebuildingqueue.php'; break;
				case 'database.addorbitalbaseshipqueue':	include SCRIPT . 'scripts/database/addorbitalbaseshipqueue.php'; break;
				case 'database.addtechnologyqueue':			include SCRIPT . 'scripts/database/addtechnologyqueue.php'; break;
				case 'database.addplayer':					include SCRIPT . 'scripts/database/addplayer.php'; break;
				case 'database.addcolor':					include SCRIPT . 'scripts/database/addcolor.php'; break;
				case 'database.addcommander':				include SCRIPT . 'scripts/database/addCommander.php'; break;
				case 'database.addreport':					include SCRIPT . 'scripts/database/addReport.php'; break;
				case 'database.addspyreport':				include SCRIPT . 'scripts/database/addspyreport.php'; break;
				case 'database.removedescriptionfromplayer':include SCRIPT . 'scripts/database/removeDescriptionFromPlayer.php'; break;
				case 'database.addstepdoneinplayer':		include SCRIPT . 'scripts/database/addStepDoneInPlayer.php'; break;
				case 'database.addrankings':				include SCRIPT . 'scripts/database/addRankings.php'; break;
				case 'database.addfaction':					include SCRIPT . 'scripts/database/addFaction.php'; break;
				case 'database.updateplayer':				include SCRIPT . 'scripts/database/updatePlayer.php'; break;

				case 'cron.daily':							include SCRIPT . 'scripts/cron/daily.php'; break;
				case 'cron.playerranking':					include SCRIPT . 'scripts/cron/playerRanking.php'; break;
				case 'cron.factionranking':					include SCRIPT . 'scripts/cron/factionRanking.php'; break;

				default: echo 'Script inconnu ou non-référencé'; break;
			}
		echo '</div>';
	}
}

include SCRIPT . 'template/close.php';
?>