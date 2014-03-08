<?php
# background paralax
echo '<div id="background-paralax" class="rank"></div>';

# inclusion des elements
include 'defaultElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	# limit
	$stepRank = 75;

	$S_PAM1 = ASM::$pam->getCurrentSession();

	ASM::$pam->newSession(FALSE);
	ASM::$pam->load(array('statement' => array(PAM_ACTIVE)), array('experience', 'DESC'), array(0, $stepRank));
	for ($i = 0; $i < ASM::$pam->size(); $i++) { 
		$player_rankXP[] = ASM::$pam->get($i);
	}
	include COMPONENT . 'rank/xp.php';

	ASM::$pam->newSession(FALSE);
	ASM::$pam->load(array('statement' => array(PAM_ACTIVE)), array('victory', 'DESC'), array(0, $stepRank));
	for ($i = 0; $i < ASM::$pam->size(); $i++) { 
		$player_rankVictory[] = ASM::$pam->get($i);
	}
	include COMPONENT . 'rank/victory.php';

	ASM::$pam->newSession(FALSE);
	ASM::$pam->load(array('statement' => array(PAM_ACTIVE)), array('defeat', 'DESC'), array(0, $stepRank));
	for ($i = 0; $i < ASM::$pam->size(); $i++) { 
		$player_rankDefeat[] = ASM::$pam->get($i);
	}
	include COMPONENT . 'rank/defeat.php';

	ASM::$pam->changeSession($S_PAM1);

	/*$db = DataBase::getInstance();
	$qr = $db->query('SELECT
			COUNT(s.id) AS nbSector,
			(SELECT COUNT(p.id) FROM player AS p WHERE p.rColor = c.id GROUP BY p.rColor) AS nbPlayer,
			c.id
		FROM sector AS s
		LEFT JOIN color AS c
			ON s.rColor = c.id
		GROUP BY s.rColor
		ORDER BY nbSector DESC, 
		nbPlayer DESC'
	);
	$factions = $qr->fetchAll();*/
echo '</div>';
?>