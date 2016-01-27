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
	include COMPONENT . 'publicity.php';

	if (!CTR::$get->exist('view') OR CTR::$get->get('view') == 'overview') {
		include_once ZEUS;

		$S_FNM_OW = ASM::$fnm->getCurrentSession();
		$TOKEN_NEWS = ASM::$fnm->newSession();
		
		if (CTR::$get->equal('news', 'list')) {
			ASM::$fnm->load(['rFaction' => $faction->id, 'pinned' => FALSE], ['dCreation', 'DESC']);
			$mode = 'all';
		} else {
			ASM::$fnm->load(['rFaction' => $faction->id, 'pinned' => TRUE]);
			$mode = 'pin';
		}

		include COMPONENT . 'faction/overview/news.php';

		ASM::$fnm->changeSession($S_FNM_OW);

		$S_PAM_1 = ASM::$pam->getCurrentSession();
		$PLAYER_GOV_TOKEN = ASM::$pam->newSession(FALSE);
		ASM::$pam->load(
			array('rColor' => CTR::$data->get('playerInfo')->get('color'), 'status' => array(6, 5, 4, 3)),
			array('status', 'DESC')
		);

		include COMPONENT . 'faction/overview/stat.php';

		$S_LAM_OLD = ASM::$lam->getCurrentsession();

		$S_LAM_ACT = ASM::$lam->newSession();
		ASM::$lam->load(array('rColor' => $faction->id, 'statement' => Law::EFFECTIVE));

		$S_LAM_VOT = ASM::$lam->newSession();
		ASM::$lam->load(array('rColor' => $faction->id, 'statement' => Law::VOTATION));

		include COMPONENT . 'faction/overview/laws.php';

		ASM::$lam->changeSession($S_LAM_OLD);
		ASM::$pam->changeSession($S_PAM_1);
	} elseif (CTR::$get->get('view') == 'forum') {
		if (!CTR::$get->exist('forum')) {
			# page d'accueil des forums
			# charge les x premiers sujets de chaque forum

			$S_TOM1 = ASM::$tom->getCurrentSession();

			$archivedMode = FALSE;

			for ($i = 1; $i <= ForumResources::size(); $i++) {
				$forum_topics = ForumResources::getInfo($i, 'id');
				
				if ($forum_topics < 10 || ($forum_topics >= 10 && $forum_topics < 20 && CTR::$data->get('playerInfo')->get('status') > 2) || ($forum_topics >= 20 && $forum_topics < 30 && CTR::$data->get('playerInfo')->get('status') == PAM_CHIEF)) {
					
					$where = [
						'rForum' => $forum_topics,
						'isArchived' => $archivedMode
					];

					if ($forum_topics < 20) {
						$where['rColor'] = CTR::$data->get('playerInfo')->get('color');
					}

					ASM::$tom->newSession();
					ASM::$tom->load(
						$where,
						['isUp', 'DESC', 'dLastMessage', 'DESC'],
						[0, 10],
						CTR::$data->get('playerId')
					);

					$topic_topics = [];

					for ($j = 0; $j < ASM::$tom->size(); $j++) { 
						$topic_topics[$j] = ASM::$tom->get($j);
					}

					$isStandard_topics = FALSE;
					$idColum_topics = $i;
					
					include COMPONENT . 'faction/forum/topics.php';
				}
			}

			ASM::$tom->changeSession($S_TOM1);
		} else {
			$forumId = !CTR::$get->exist('forum') ? 1 : CTR::$get->get('forum');
			$archivedMode = CTR::$get->equal('mode', 'archived') && in_array(CTR::$data->get('playerInfo')->get('status'), [PAM_CHIEF, PAM_WARLORD, PAM_TREASURER, PAM_MINISTER])
				? TRUE : FALSE;
				
			if ($forumId < 10 || ($forumId >= 10 && $forumId < 20 && CTR::$data->get('playerInfo')->get('status') > 2) || ($forumId >= 20 && $forumId < 30 && CTR::$data->get('playerInfo')->get('status') == PAM_CHIEF)) {
				# forum component
				include COMPONENT . 'faction/forum/forum.php';

				$where = [
					'rForum' => $forumId,
					'isArchived' => $archivedMode
				];

				if ($forumId < 20) {
					$where['rColor'] = CTR::$data->get('playerInfo')->get('color');
				}

				$S_TOM1 = ASM::$tom->getCurrentSession();
				ASM::$tom->newSession();
				ASM::$tom->load(
					$where,
					['isUp', 'DESC', 'dLastMessage', 'DESC'],
					[0, 10],
					CTR::$data->get('playerId')
				);

				$topic_topics = array();
				for ($i = 0; $i < ASM::$tom->size(); $i++) { 
					$topic_topics[$i] = ASM::$tom->get($i);
				}
				
				$isStandard_topics = TRUE;
				$forum_topics = $forumId;

				include COMPONENT . 'faction/forum/topics.php';

				ASM::$tom->changeSession($S_TOM1);
			} else {
				CTR::redirect('faction/view-forum');
			}

			if (CTR::$get->exist('topic')) {
				# topic component
				$S_TOM2 = ASM::$tom->getCurrentSession();
				ASM::$tom->newSession();

				if ($forumId < 10 || ($forumId >= 10 && $forumId < 20 && CTR::$data->get('playerInfo')->get('status') > 2)) {
					ASM::$tom->load(
						['id' => CTR::$get->get('topic'), 'rColor' => CTR::$data->get('playerInfo')->get('color'), 'rForum' => $forumId],
						[], [],
						CTR::$data->get('playerId')
					);
				} else if ($forumId >= 20 && $forumId < 30 && CTR::$data->get('playerInfo')->get('status') == PAM_CHIEF) {
					ASM::$tom->load(
						['id' => CTR::$get->get('topic'), 'rForum' => $forumId],
						[], [],
						CTR::$data->get('playerId')
					);
				}

				if (ASM::$tom->size() == 0) {
					CTR::$alert->add('Les données sont illisibles, les messages doivent sûrement être cryptés !', ALERT_STD_ERROR);
				} else {
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

					if (in_array(CTR::$data->get('playerInfo')->get('status'), [PAM_CHIEF, PAM_WARLORD, PAM_TREASURER, PAM_MINISTER])) {
						include COMPONENT . 'faction/forum/manage-topic.php';
					}
					ASM::$fmm->changeSession($S_FMM1);
				}
				ASM::$tom->changeSession($S_TOM2);
			} elseif (CTR::$get->exist('mode') && CTR::$get->get('mode') == 'create') {
				# créer un topic
				include COMPONENT . 'faction/forum/createTopic.php';
			} else {
				include COMPONENT . 'default.php';
			}
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
			include COMPONENT . 'faction/data/war/reports-attack.php';
			include COMPONENT . 'faction/data/war/reports-defend.php';
			include COMPONENT . 'faction/data/war/levels.php';
		} elseif (CTR::$get->get('mode') == 'tactical') {
			include COMPONENT . 'faction/data/tactical/map.php';
			include COMPONENT . 'faction/data/tactical/sectors.php';
			include COMPONENT . 'faction/data/tactical/targets.php';
		} elseif (CTR::$get->get('mode') == 'diplomacy') {
			include COMPONENT . 'faction/data/diplomacy/main.php';
			include COMPONENT . 'faction/data/diplomacy/about.php';
			include COMPONENT . 'default.php';
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
		if (in_array(CTR::$data->get('playerInfo')->get('status'), [PAM_CHIEF, PAM_WARLORD, PAM_TREASURER, PAM_MINISTER])) {
			$S_PAM_OLD = ASM::$pam->getCurrentSession();
			$PLAYER_SENATE_TOKEN = ASM::$pam->newSession();
			ASM::$pam->load(array('rColor' => $faction->id, 'status' => PAM_PARLIAMENT, 'statement' => [PAM_ACTIVE, PAM_INACTIVE, PAM_HOLIDAY]));

			include COMPONENT . 'faction/government/nav.php';

			if (!CTR::$get->exist('mode') OR CTR::$get->get('mode') == 'law') {
				$S_SEM_OLD = ASM::$sem->getCurrentsession();
				$S_SEM_LAW = ASM::$sem->newSession();
				ASM::$sem->load(array('rColor' => $faction->id));

				$nbLaws = 0;
				$nbPlayer = PlayerManager::count(['rColor' => $faction->id, 'statement' => [PAM_ACTIVE, PAM_INACTIVE, PAM_HOLIDAY]]);
				
				for ($i = 1; $i <= LawResources::size(); $i++) {
					if (LawResources::getInfo($i, 'department') == CTR::$data->get('playerInfo')->get('status') AND LawResources::getInfo($i, 'isImplemented')) {
						$governmentLaw_id = $i;
						$nbLaws++;
						include COMPONENT . 'faction/government/law.php';
					}
				}

				if (2 - $nbLaws > 0) {
					for ($i = 0; $i < 2 - $nbLaws; $i++) { 
						include COMPONENT . 'default.php';
					}
				}
				ASM::$sem->changeSession($S_SEM_OLD);
			} elseif (CTR::$get->get('mode') == 'news') {
				$S_FNM_1 = ASM::$fnm->getCurrentSession();
				$TOKEN_NEWS_LIST = ASM::$fnm->newSession();
				ASM::$fnm->load(['rFaction' => $faction->id], ['dCreation', 'DESC']);
				
				include COMPONENT . 'faction/news/list.php';

				if (CTR::$get->exist('news')) {
					if (ASM::$fnm->getById(CTR::$get->get('news')) !== FALSE) {
						include COMPONENT . 'faction/news/edit.php';
					} else {
						CTR::redirect('faction/view-government/mode-news');
					}
				} else {
					include COMPONENT . 'faction/news/create.php';
				}

				ASM::$fnm->changeSession($S_FNM_1);
			} elseif (CTR::$get->get('mode') == 'message') {
				include COMPONENT . 'faction/government/message.php';
			} elseif (CTR::$get->get('mode') == 'description') {
				include COMPONENT . 'faction/government/description.php';
			} elseif (CTR::$get->get('mode') == 'credit') {
				include COMPONENT . 'faction/government/credit.php';
			} elseif (CTR::$get->get('mode') == 'manage') {
				$PLAYER_GOV_TOKEN = ASM::$pam->newSession(FALSE);
				ASM::$pam->load(
					array('rColor' => $faction->id, 'status' => [PAM_CHIEF, PAM_WARLORD, PAM_TREASURER, PAM_MINISTER]),
					array('status', 'DESC')
				);

				include COMPONENT . 'faction/government/manage/main.php';
				include COMPONENT . 'default.php';

			} else {
				CTR::redirect('faction');
			}
			
			ASM::$pam->changeSession($S_PAM_OLD);
		} else {
			CTR::redirect('faction');
		}
	} elseif (CTR::$get->get('view') == 'senate') {
		if (in_array(CTR::$data->get('playerInfo')->get('status'), [PAM_CHIEF, PAM_WARLORD, PAM_TREASURER, PAM_MINISTER, PAM_PARLIAMENT])) {
			$S_VLM_OLD = ASM::$vlm->getCurrentsession();
			$S_LAM_OLD = ASM::$lam->getCurrentsession();

			$S_LAM_TOVOTE = ASM::$lam->newSession();
			ASM::$lam->load(array('rColor' => $faction->id, 'statement' => Law::VOTATION));
			include COMPONENT . 'faction/senate/stats.php';

			for ($i = 0; $i < ASM::$lam->size(); $i++) {
				$law = ASM::$lam->get($i);

				$S_VLM_TOVOTE = ASM::$vlm->newSession();
				ASM::$vlm->load(array('rLaw' => $law->id));

				include COMPONENT . 'faction/senate/law.php';
			}
			if (ASM::$lam->size() < 1) {
				include COMPONENT . 'default.php';
			}

			$S_LAM_HISTORIC = ASM::$lam->newSession();
			ASM::$lam->load(
				['rColor' => $faction->id, 'statement' => [Law::EFFECTIVE, Law::OBSOLETE, Law::REFUSED]],
				['dCreation', 'DESC'],
				[0, 10]
			);

			include COMPONENT . 'faction/senate/historic.php';

			ASM::$lam->changeSession($S_LAM_OLD);
			ASM::$vlm->changeSession($S_VLM_OLD);
		} else {
			CTR::redirect('faction');
		}
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
				CTR::redirect('faction');
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

			if ($faction->regime == Color::DEMOCRATIC) {
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
				} else {
					include COMPONENT . 'default.php';
				}
			} elseif ($faction->regime == Color::ROYALISTIC) {
				$candidat  = ASM::$cam->get(0);
				$rElection = ASM::$elm->get(0)->id;

				include COMPONENT . 'faction/election/putsch.php';
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

		$PAM_LAST_TOKEN = ASM::$pam->newSession(FALSE);
		ASM::$pam->load(
			['rColor' => CTR::$data->get('playerInfo')->get('color')], 
			['dInscription', 'DESC'],
			[0, 25]
		);

		ASM::$pam->newSession(FALSE);
		ASM::$pam->load(
			['rColor' => CTR::$data->get('playerInfo')->get('color'), 'statement' => [PAM_ACTIVE, PAM_INACTIVE, PAM_HOLIDAY]], 
			['status', 'DESC', 'factionPoint', 'DESC']
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

		include COMPONENT . 'faction/player/statPlayer.php';
		include COMPONENT . 'faction/player/listPlayer.php';

		ASM::$pam->changeSession($S_PAM1);
	} else {
		CTR::redirect('faction');
	}
echo '</div>';

ASM::$clm->changeSession($S_COL1);
?>