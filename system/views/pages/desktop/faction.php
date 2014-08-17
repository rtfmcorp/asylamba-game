<?php
# inclusion des modules
include_once DEMETER;

# factionNav component
$color_factionNav = CTR::$data->get('playerInfo')->get('color');

$S_COL1 = ASM::$clm->getCurrentSession();
ASM::$clm->newSession();
ASM::$clm->load(array('id' => $color_factionNav));

if (ASM::$clm->size() == 1) {
	$faction = ASM::$clm->get(0);
} else {
	CTR::redirect('profil');
}

# background paralax
echo '<div id="background-paralax" class="profil"></div>';

# inclusion des elements
include 'factionElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	if (!CTR::$get->exist('view') OR CTR::$get->get('view') == 'forum') {
		# forum component
		include COMPONENT . 'demeter/forum/forum.php';

		# topics component
		$forumId = !CTR::$get->exist('forum')
			? 1
			: CTR::$get->get('forum');

		$S_TOM1 = ASM::$tom->getCurrentSession();
		ASM::$tom->newSession();
		ASM::$tom->load(
			array(
				'rForum' => $forumId, 
				'rColor' => CTR::$data->get('playerInfo')->get('color'), 
				'statement' => array(ForumTopic::PUBLISHED, ForumTopic::RESOLVED)
			),
			array('dLastMessage', 'DESC'),
			array(),
			CTR::$data->get('playerId')
		);

		$topic_topics = array();
		for ($i = 0; $i < ASM::$tom->size(); $i++) { 
			$topic_topics[$i] = ASM::$tom->get($i);
		}
		$forum_topics = $forumId;
		include COMPONENT . 'demeter/forum/topics.php';

		if (CTR::$get->exist('topic')) {
			# topic component
			$topic_topic = ASM::$tom->getById(CTR::$get->get('topic'));
			$topic_topic->updateLastView(CTR::$data->get('playerId'));

			$S_FMM1 = ASM::$fmm->getCurrentSession();
			ASM::$fmm->newSession();
			ASM::$fmm->load(array('rTopic' => $topic_topic->id), array('dCreation', 'DESC', 'id', 'DESC'));

			$message_topic = array();
			for ($i = 0; $i < ASM::$fmm->size(); $i++) { 
				$message_topic[$i] = ASM::$fmm->get($i);
			}

			include COMPONENT . 'demeter/forum/topic.php';

			ASM::$fmm->changeSession($S_FMM1);
		} elseif (CTR::$get->exist('mode') && CTR::$get->get('mode') == 'create') {
			# créer un topic
			include COMPONENT . 'demeter/forum/createTopic.php';
		} else {
			include COMPONENT . 'default.php';
		}

		ASM::$tom->changeSession($S_TOM1);
	} elseif (CTR::$get->get('view') == 'government') {
		include_once ZEUS;

		$S_PAM_1 = ASM::$pam->getCurrentSession();
		$S_PAM_N3 = ASM::$pam->newSession(FALSE);
		ASM::$pam->load(
			array('rColor' => CTR::$data->get('playerInfo')->get('color'), 'status' => array(6, 5, 4, 3)),
			array('status', 'DESC')
		);

		$S_PAM_N2 = ASM::$pam->newSession(FALSE);
		ASM::$pam->load(
			array('rColor' => CTR::$data->get('playerInfo')->get('color'), 'status' => PAM_PARLIAMENT),
			array('factionPoint', 'DESC')
		);

		$PLAYER_GOV_TOKEN = $S_PAM_N3;
		$PLAYER_SENATE_TOKEN = $S_PAM_N2 ;
		include COMPONENT . 'demeter/government/government.php';
		include COMPONENT . 'demeter/government/senate.php';

		ASM::$pam->changeSession($S_PAM_1);
	} elseif (CTR::$get->get('view') == 'election' && in_array($faction->electionStatement, array(Color::CAMPAIGN, Color::ELECTION))) {
		if ($faction->electionStatement == Color::CAMPAIGN) {
			$S_ELM_1 = ASM::$elm->getCurrentSession();
			$ELM_CAMPAIGN_TOKEN = ASM::$elm->newSession();
			ASM::$elm->load(array('rColor' => $faction->id), array('id', 'DESC'), array(0, 1));

			if (ASM::$elm->size()) {
				$S_CAM_1 = ASM::$cam->getCurrentSession();
				$S_CAM_CAN = ASM::$cam->newSession();
				ASM::$cam->load(array('rElection' => ASM::$elm->get(0)->id));

				$S_VOM_1 = ASM::$vom->getCurrentSession();
				$VOM_ELC_TOKEN = ASM::$vom->newSession();
				ASM::$vom->load(array('rPlayer' => CTR::$data->get('playerId'), 'rElection' => ASM::$elm->get(0)->id));

				$nbCandidate = ASM::$cam->size();
				include COMPONENT . 'demeter/election/campaign.php';
				include COMPONENT . 'demeter/election/candidate.php';

				ASM::$cam->changeSession($S_CAM_1);
				ASM::$elm->changeSession($S_ELM_1);
			} else {
				CTR::redirect('404');
			}
			ASM::$vom->changeSession($S_VOM_1);
		} elseif ($faction->electionStatement == Color::ELECTION) {
			$S_ELM_1 = ASM::$elm->getCurrentSession();
			$ELM_ELECTION_TOKEN = ASM::$elm->newSession();
			ASM::$elm->load(array('rColor' => $faction->id), array('id', 'DESC'), array(0, 1));

			$S_CAM_1 = ASM::$cam->getCurrentSession();
			$S_CAM_CAN = ASM::$cam->newSession();
			ASM::$cam->load(array('rElection' => ASM::$elm->get(0)->id));

			$S_VOM_1 = ASM::$vom->getCurrentSession();
			$VOM_ELC_TOKEN = ASM::$vom->newSession();
			ASM::$vom->load(array('rPlayer' => CTR::$data->get('playerId'), 'rElection' => ASM::$elm->get(0)->id));

			$nbCandidate = ASM::$cam->size();
			include COMPONENT . 'demeter/election/election.php';

			$rElection = ASM::$elm->get(0)->id;
			include COMPONENT . 'demeter/election/candidate.php';

			ASM::$cam->changeSession($S_CAM_1);
			ASM::$elm->changeSession($S_ELM_1);
			ASM::$vom->changeSession($S_VOM_1);
		}
	} elseif (CTR::$get->get('view') == 'player') {
		# vue des joueurs, a supprimer

		include_once ZEUS;
		$S_PAM1 = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession(FALSE);
		ASM::$pam->load(array('rColor' => CTR::$data->get('playerInfo')->get('color')), array('factionPoint', 'DESC'));

		# statPlayer component
		$nbPlayer_statPlayer = 0;

		$nbOnlinePlayer_statPlayer = 0;
		$nbOfflinePlayer_statPlayer = 0;

		$avgVictoryPlayer_statPlayer = 0;
		$avgDefeatPlayer_statPlayer = 0;
		$avgPointsPlayer_statPlayer = 0;

		# listPlayer component
		$players_listPlayer = array();

		# worker
		for ($i = 0; $i < ASM::$pam->size(); $i++) { 
			$player = ASM::$pam->get($i);

			$nbPlayer_statPlayer++;

			if (Utils::interval(Utils::now(), $player->getDLastActivity(), 's') < 600) {
				$nbOnlinePlayer_statPlayer++;
			} else {
				$nbOfflinePlayer_statPlayer++;
			}

			$avgVictoryPlayer_statPlayer += $player->getVictory();
			$avgDefeatPlayer_statPlayer += $player->getDefeat();
			$avgPointsPlayer_statPlayer += $player->getExperience();

			$players_listPlayer[] = $player;
		}

		$avgVictoryPlayer_statPlayer = Format::numberFormat($avgVictoryPlayer_statPlayer / $nbPlayer_statPlayer, 2);
		$avgDefeatPlayer_statPlayer = Format::numberFormat($avgDefeatPlayer_statPlayer / $nbPlayer_statPlayer, 2);
		$avgPointsPlayer_statPlayer = Format::numberFormat($avgPointsPlayer_statPlayer / $nbPlayer_statPlayer, 2);

		include COMPONENT . 'demeter/player/statPlayer.php';
		include COMPONENT . 'demeter/player/listPlayer.php';

		ASM::$pam->changeSession($S_PAM1);
	} else {
		CTR::redirect('404');
	}
echo '</div>';

ASM::$clm->changeSession($S_COL1);
?>