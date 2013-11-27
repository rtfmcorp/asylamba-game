<?php
# background paralax
echo '<div id="background-paralax" class="profil"></div>';

# inclusion des elements
include 'factionElement/movers.php';
include 'factionElement/subnav.php';

# contenu spécifique
echo '<div id="content">';
	# inclusion des modules
	include_once DEMETER;

	# factionNav component
	$color_factionNav = CTR::$data->get('playerInfo')->get('color');
	include COMPONENT . 'demeter/factionNav.php';

	if (!CTR::$get->exist('view') OR CTR::$get->get('view') == 'forum') {
		# forum component
		include COMPONENT . 'demeter/forum/forum.php';

		# topics component
		if (!CTR::$get->exist('forum')) {
			$forumId = 1;
		} else {
			$forumId = CTR::$get->get('forum');
		}

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
			ASM::$fmm->load(array('rTopic' => $topic_topic->id));

			$message_topic = array();
			for ($i = 0; $i < ASM::$fmm->size(); $i++) { 
				$message_topic[$i] = ASM::$fmm->get($i);
			}

			include COMPONENT . 'demeter/forum/topic.php';

			ASM::$fmm->changeSession($S_FMM1);
		} elseif (CTR::$get->exist('mode') && CTR::$get->get('mode') == 'create') {
			# créer un topic
			include COMPONENT . 'demeter/forum/createTopic.php';
		}

		ASM::$tom->changeSession($S_TOM1);
	} elseif (CTR::$get->get('view') == 'player') {
		# vue des joueurs, a supprimer

		include_once ZEUS;
		$S_PAM1 = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession(FALSE);
		ASM::$pam->load(array('rColor' => CTR::$data->get('playerInfo')->get('color')), array('experience', 'DESC'));

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
?>