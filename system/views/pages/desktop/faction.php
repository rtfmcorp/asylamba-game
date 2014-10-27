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
	if (!CTR::$get->exist('view') OR CTR::$get->get('view') == 'overview') {
		include_once ZEUS;

		$S_PAM_1 = ASM::$pam->getCurrentSession();
		$PLAYER_GOV_TOKEN = ASM::$pam->newSession(FALSE);
		ASM::$pam->load(
			array('rColor' => CTR::$data->get('playerInfo')->get('color'), 'status' => array(6, 5, 4, 3)),
			array('status', 'DESC')
		);

		include COMPONENT . 'faction/overview/news.php';
		include COMPONENT . 'faction/overview/stat.php';
		include COMPONENT . 'faction/overview/laws.php';

		ASM::$pam->changeSession($S_PAM_1);
	} elseif (CTR::$get->get('view') == 'forum') {
		if (!CTR::$get->exist('forum')) {
			# page d'accueil des forums
			# charge les x premiers sujets de chaque forum

			$S_TOM1 = ASM::$tom->getCurrentSession();

			for ($i = 1; $i <= ForumResources::size(); $i++) { 
				ASM::$tom->newSession();
				ASM::$tom->load(
					array(
						'rForum' => ForumResources::getInfo($i, 'id'), 
						'rColor' => CTR::$data->get('playerInfo')->get('color'), 
						'statement' => array(ForumTopic::PUBLISHED, ForumTopic::RESOLVED)
					),
					array('dLastMessage', 'DESC'),
					array(0, 10),
					CTR::$data->get('playerId')
				);

				$topic_topics = array();
				for ($j = 0; $j < ASM::$tom->size(); $j++) { 
					$topic_topics[$j] = ASM::$tom->get($j);
				}
				$forum_topics = ForumResources::getInfo($i, 'id');
				$isStandard_topics = FALSE;
				$idColum_topics = $i;

				if (ForumResources::getInfo($i, 'id') < 10) {
					include COMPONENT . 'faction/forum/topics.php';
				}
			}

			ASM::$tom->changeSession($S_TOM1);
		} else {
			# forum component
			include COMPONENT . 'faction/forum/forum.php';

			# topics component
			$forumId = !CTR::$get->exist('forum')
				? 1
				: CTR::$get->get('forum');

			$S_TOM1 = ASM::$tom->getCurrentSession();
			ASM::$tom->newSession();
			if ($forumId < 20) {
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
			} else {
				ASM::$tom->load(
					array(
						'rForum' => $forumId,
						'statement' => array(ForumTopic::PUBLISHED, ForumTopic::RESOLVED)
					),
					array('dLastMessage', 'DESC'),
					array(),
					CTR::$data->get('playerId')
				);
			}

			$topic_topics = array();
			for ($i = 0; $i < ASM::$tom->size(); $i++) { 
				$topic_topics[$i] = ASM::$tom->get($i);
			}
			$isStandard_topics = TRUE;
			$forum_topics = $forumId;

			if ($forumId < 10) {
				include COMPONENT . 'faction/forum/topics.php';
			} elseif ($forumId >= 10 && $forumId < 20 && CTR::$data->get('playerInfo')->get('status') > 2) {
				include COMPONENT . 'faction/forum/topics.php';
			} elseif ($forumId >= 20 && CTR::$data->get('playerInfo')->get('status') == PAM_CHIEF) {
				include COMPONENT . 'faction/forum/topics.php';
			} else {
				CTR::redirect('faction/view-forum');
			}

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

				include COMPONENT . 'faction/forum/topic.php';

				ASM::$fmm->changeSession($S_FMM1);
			} elseif (CTR::$get->exist('mode') && CTR::$get->get('mode') == 'create') {
				# créer un topic
				include COMPONENT . 'faction/forum/createTopic.php';
			} else {
				include COMPONENT . 'default.php';
			}

			ASM::$tom->changeSession($S_TOM1);
		}
	} elseif (CTR::$get->get('view') == 'data') {
		include COMPONENT . 'faction/data/nav.php';

		if (!CTR::$get->exist('mode') OR CTR::$get->get('mode') == 'financial') {
			include COMPONENT . 'faction/data/financial/stats.php';
			include COMPONENT . 'faction/data/financial/sectors-tax.php';
			include COMPONENT . 'faction/data/financial/donations.php';
		} elseif (CTR::$get->get('mode') == 'trade') {
			include COMPONENT . 'faction/data/trade/rc-stats.php';
			include COMPONENT . 'faction/data/trade/tax-out.php';
			include COMPONENT . 'faction/data/trade/tax-in.php';
		} elseif (CTR::$get->get('mode') == 'war') {
			include COMPONENT . 'faction/data/war/stats.php';
			include COMPONENT . 'faction/data/war/sectors.php';
			include COMPONENT . 'faction/data/war/incoming.php';
			include COMPONENT . 'faction/data/war/levels.php';
		} elseif (CTR::$get->get('mode') == 'law') {
			$listlaw_status = 6;
			include COMPONENT . 'faction/data/law/list.php';
			$listlaw_status = 3;
			include COMPONENT . 'faction/data/law/list.php';
			$listlaw_status = 4;
			include COMPONENT . 'faction/data/law/list.php';
			$listlaw_status = 5;
			include COMPONENT . 'faction/data/law/list.php';
		}
	} elseif (CTR::$get->get('view') == 'government') {
	/**/
	} elseif (CTR::$get->get('view') == 'election' && in_array($faction->electionStatement, array(Color::CAMPAIGN, Color::ELECTION))) {
		if ($faction->electionStatement == Color::CAMPAIGN) {
			$S_ELM_1 = ASM::$elm->getCurrentSession();
			$ELM_CAMPAIGN_TOKEN = ASM::$elm->newSession();
			ASM::$elm->load(array('rColor' => $faction->id), array('id', 'DESC'), array(0, 1));

			if (ASM::$elm->size()) {
				$S_CAM_1 = ASM::$cam->getCurrentSession();
				$S_CAM_CAN = ASM::$cam->newSession();
				ASM::$cam->load(array('rElection' => ASM::$elm->get(0)->id));

				$nbCandidate = ASM::$cam->size();
				include COMPONENT . 'faction/election/campaign.php';
				include COMPONENT . 'faction/election/list.php';

				if (CTR::$get->equal('candidate', 'create')) {
					include COMPONENT . 'faction/election/postulate.php';
				} elseif (CTR::$get->exist('candidate') AND ASM::$cam->getById(CTR::$get->get('candidate')) !== FALSE) {
					$candidat = ASM::$cam->getById(CTR::$get->get('candidate'));

					include COMPONENT . 'faction/election/candidate.php';

					ASM::$tom->load(
						array(
							'rForum' => 30, 
							'rPlayer' => $candidat->rPlayer
						),
						array('id', 'DESC'),
						array(0, 1),
						CTR::$data->get('playerId')
					);

					if (ASM::$tom->size() == 1) {
						$topic_topic = ASM::$tom->get(0);
						$topic_topic->updateLastView(CTR::$data->get('playerId'));

						$S_FMM1 = ASM::$fmm->getCurrentSession();
						ASM::$fmm->newSession();
						ASM::$fmm->load(array('rTopic' => $topic_topic->id), array('dCreation', 'DESC', 'id', 'DESC'));

						$message_topic = array();
						for ($i = 0; $i < ASM::$fmm->size(); $i++) { 
							$message_topic[$i] = ASM::$fmm->get($i);
						}

						include COMPONENT . 'faction/forum/topic.php';

						ASM::$fmm->changeSession($S_FMM1);
					}
				} else {
					include COMPONENT . 'default.php';
				}

				ASM::$cam->changeSession($S_CAM_1);
			} else {
				CTR::redirect('404');
			}
			ASM::$elm->changeSession($S_ELM_1);
		} elseif ($faction->electionStatement == Color::ELECTION) {
			$S_ELM_1 = ASM::$elm->getCurrentSession();
			$S_CAM_1 = ASM::$cam->getCurrentSession();
			$S_VOM_1 = ASM::$vom->getCurrentSession();
			$S_PAM_1 = ASM::$pam->getCurrentSession();

			$ELM_ELECTION_TOKEN = ASM::$elm->newSession();
			ASM::$elm->load(array('rColor' => $faction->id), array('id', 'DESC'), array(0, 1));

			$S_CAM_CAN = ASM::$cam->newSession();
			ASM::$cam->load(array('rElection' => ASM::$elm->get(0)->id));

			$VOM_ELC_TOKEN = ASM::$vom->newSession();
			ASM::$vom->load(array('rPlayer' => CTR::$data->get('playerId'), 'rElection' => ASM::$elm->get(0)->id));

			$VOM_ELC_TOTAL_TOKEN = ASM::$vom->newSession();
			ASM::$vom->load(array('rElection' => ASM::$elm->get(0)->id));

			$PAM_ELC_TOKEN = ASM::$pam->newSession(FALSE);
			ASM::$pam->load(array('rColor' => CTR::$data->get('playerInfo')->get('color')));

			if ($faction->getRegime() == Color::DEMOCRATIC) {

				$nbCandidate = ASM::$cam->size();
				include COMPONENT . 'faction/election/election.php';

				$rElection = ASM::$elm->get(0)->id;
				include COMPONENT . 'faction/election/list.php';

				if (CTR::$get->exist('candidate') AND ASM::$cam->getById(CTR::$get->get('candidate')) !== FALSE) {
					$candidat = ASM::$cam->getById(CTR::$get->get('candidate'));

					include COMPONENT . 'faction/election/candidate.php';

					ASM::$tom->load(
						array(
							'rForum' => 30, 
							'rPlayer' => $candidat->rPlayer
						),
						array('id', 'DESC'),
						array(0, 1),
						CTR::$data->get('playerId')
					);

					if (ASM::$tom->size() == 1) {
						$topic_topic = ASM::$tom->get(0);
						$topic_topic->updateLastView(CTR::$data->get('playerId'));

						$S_FMM1 = ASM::$fmm->getCurrentSession();
						ASM::$fmm->newSession();
						ASM::$fmm->load(array('rTopic' => $topic_topic->id), array('dCreation', 'DESC', 'id', 'DESC'));

						$message_topic = array();
						for ($i = 0; $i < ASM::$fmm->size(); $i++) { 
							$message_topic[$i] = ASM::$fmm->get($i);
						}
						$election_topic = TRUE;
						include COMPONENT . 'faction/forum/topic.php';

						ASM::$fmm->changeSession($S_FMM1);
					}
				}
			} elseif ($faction->getRegime() == Color::ROYALISTIC) {
				# time variables
				$startPutsch  = $faction->dLastElection;
				$endPutsch    = Utils::addSecondsToDate($faction->dLastElection, Color::PUTSCHTIME);
				$remainPutsch = Utils::interval($startPutsch, $endPutsch, 's');
				include COMPONENT . 'faction/election/putsch.php';

				$candidat = ASM::$cam->get(0);
				include COMPONENT . 'faction/election/candidate.php';

				ASM::$tom->load(
					array(
						'rForum' => 30, 
						'rPlayer' => $candidat->rPlayer
					),
					array('id', 'DESC'),
					array(0, 1),
					CTR::$data->get('playerId')
				);

				if (ASM::$tom->size() == 1) {
					$topic_topic = ASM::$tom->get(0);
					$topic_topic->updateLastView(CTR::$data->get('playerId'));

					$S_FMM1 = ASM::$fmm->getCurrentSession();
					ASM::$fmm->newSession();
					ASM::$fmm->load(array('rTopic' => $topic_topic->id), array('dCreation', 'DESC', 'id', 'DESC'));

					$message_topic = array();
					for ($i = 0; $i < ASM::$fmm->size(); $i++) { 
						$message_topic[$i] = ASM::$fmm->get($i);
					}
					$election_topic = TRUE;
					include COMPONENT . 'faction/forum/topic.php';

					ASM::$fmm->changeSession($S_FMM1);
				}
			} else {
				include COMPONENT . 'default.php';
			}

			ASM::$cam->changeSession($S_CAM_1);
			ASM::$elm->changeSession($S_ELM_1);
			ASM::$vom->changeSession($S_VOM_1);
			ASM::$pam->changeSession($S_PAM_1);
		}
	} elseif (CTR::$get->get('view') == 'player') {
		include_once ZEUS;
		$S_PAM1 = ASM::$pam->getCurrentSession();
		ASM::$pam->newSession(FALSE);
		ASM::$pam->load(
			array('rColor' => CTR::$data->get('playerInfo')->get('color')), 
			array('status', 'DESC', 'factionPoint', 'DESC')
		);

		# statPlayer component
		$nbPlayer_statPlayer = ASM::$clm->getById(CTR::$data->get('playerInfo')->get('color'))->activePlayers;

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

		$avgVictoryPlayer_statPlayer = $nbPlayer_statPlayer != 0
			? Format::numberFormat($avgVictoryPlayer_statPlayer / $nbPlayer_statPlayer, 2)
			: 0;
		$avgDefeatPlayer_statPlayer = $nbPlayer_statPlayer != 0
			? Format::numberFormat($avgDefeatPlayer_statPlayer / $nbPlayer_statPlayer, 2)
			: 0;
		$avgPointsPlayer_statPlayer = $nbPlayer_statPlayer != 0
			? Format::numberFormat($avgPointsPlayer_statPlayer / $nbPlayer_statPlayer, 2)
			: 0;

		include COMPONENT . 'faction/player/statPlayer.php';
		include COMPONENT . 'faction/player/listPlayer.php';

		ASM::$pam->changeSession($S_PAM1);
	} else {
		CTR::redirect('404');
	}
echo '</div>';

ASM::$clm->changeSession($S_COL1);
?>