<?php
# WORK PART
###########
include_once GAIA;
include_once ATHENA;

# load notif
include_once HERMES;
$S_NTM1 = ASM::$ntm->getCurrentSession();
ASM::$ntm->newSession();
ASM::$ntm->load(array('rPlayer' => CTR::$data->get('playerId'), 'readed' => 0), array('dSending', 'DESC'));

# load message
/*$db = DataBase::getInstance();
$qr = $db->prepare('SELECT COUNT(id) AS n FROM message WHERE readed = 0 AND rPlayerReader = ? GROUP BY rPlayerReader');
$qr->execute(array(CTR::$data->get('playerId')));
$aw = $qr->fetch();
$message = (count($aw['n']) > 0) ? $aw['n'] : 0;*/
$message = 0;

# DISPLAY NAV BAR
#################
echo '<div id="nav">';
	echo '<div class="box left">';
		echo '<a href="#" class="square sh" data-target="change-bases"><img src="' . MEDIA . 'common/nav-base.png" alt="" /></a>';

		# select current base name
		$currentBaseName = NULL;
		$currentBaseImg  = NULL;
		for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
			if (CTR::$data->get('playerParams')->get('base') == CTR::$data->get('playerBase')->get('ob')->get($i)->get('id')) {
				$currentBaseName = CTR::$data->get('playerBase')->get('ob')->get($i)->get('name');
				$currentBaseImg  = CTR::$data->get('playerBase')->get('ob')->get($i)->get('img');
				break;
			}
		}

		if (CTR::$data->get('playerBase')->get('ob')->get(0)) {
			$nextBaseId = CTR::$data->get('playerBase')->get('ob')->get(0)->get('id');
			$finded = FALSE;
			for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
				if ($finded) {
					$nextBaseId = CTR::$data->get('playerBase')->get('ob')->get($i)->get('id');
					break;
				}
				if (CTR::$data->get('playerParams')->get('base') == CTR::$data->get('playerBase')->get('ob')->get($i)->get('id')) {
					$finded = TRUE;
				}
			}
		} else {
			$nextBaseId = 0;
			$currentBaseName = 'Reconnectez-vous';
			$currentBaseImg = '1-1';
		}

		$isActive = (in_array(CTR::getPage(), array('bases'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'bases" class="current-base ' . $isActive . '">';
			echo '<img src="' . MEDIA . 'map/place/place' . $currentBaseImg . '.png" alt="' . $currentBaseName . '" /> ';
			echo $currentBaseName;
		echo '</a>';
		echo '<a href="' . Format::actionBuilder('switchbase', ['base' => $nextBaseId]) . '" class="square">';
			echo '<img src="' . MEDIA . 'common/next-base.png" alt="base suivante" />';
		echo '</a>';
	echo '</div>';

	echo '<div class="box left-2">';
		$isActive = (in_array(CTR::getPage(), array('profil'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'profil" class="square hb rb ' . $isActive . '" title="profil"><img src="' . MEDIA . 'common/nav-profil.png" alt="" /></a>';

		$isActive = (in_array(CTR::getPage(), array('fleet'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'fleet" class="square hb rb ' . $isActive . '" title="flottes"><img src="' . MEDIA . 'common/nav-fleet.png" alt="" /></a>';

		$isActive = (in_array(CTR::getPage(), array('map'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'map" class="square hb rb ' . $isActive . '" title="galaxie"><img src="' . MEDIA . 'common/nav-map.png" alt="" /></a>';
		
		$isActive = (in_array(CTR::getPage(), array('financial'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'financial" class="square hb rb ' . $isActive . '" title="finances"><img src="' . MEDIA . 'common/nav-financial.png" alt="" /></a>';
	
		$isActive = (in_array(CTR::getPage(), array('technology'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'technology" class="square hb rb ' . $isActive . '" title="université"><img src="' . MEDIA . 'common/nav-techno.png" alt="" /></a>';
		
		$isActive = (in_array(CTR::getPage(), array('faction'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'faction" class="square hb rb ' . $isActive . '" title="faction"><img src="' . MEDIA . 'common/nav-faction.png" alt="" /></a>';
	echo '</div>';

	echo '<div class="box left-3">';
		$isActive = (in_array(CTR::getPage(), array('rank'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'rank" class="square hb rb ' . $isActive . '" title="classements"><img src="' . MEDIA . 'common/nav-rank.png" alt="" /></a>';

		$isActive = (in_array(CTR::getPage(), array('embassy'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'embassy" class="square hb rb ' . $isActive . '" title="ambassades"><img src="' . MEDIA . 'common/nav-embassy.png" alt="" /></a>';

		$isActive = (in_array(CTR::getPage(), array('message'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'message" class="square hb rb ' . $isActive . '" title="messagerie"><img src="' . MEDIA . 'common/nav-message.png" alt="" />';
			echo ($message > 0) ? '<span class="number">' . $message . '</span>' : NULL;
		echo '</a>';

		echo '<a href="' . APP_ROOT . 'message" id="general-notif-container" class="square sh" data-target="new-notifications"><img src="' . MEDIA . 'common/nav-notif.png" alt="" />';
			echo (ASM::$ntm->size() > 0) ? '<span class="number">' . ASM::$ntm->size() . '</span>' : NULL;
		echo '</a>';
	echo '</div>';

	echo '<div class="box right">';
		if (CTR::$data->get('playerInfo')->get('admin') == TRUE) {
			$isActive = (in_array(CTR::getPage(), array('admin'))) ? 'active' : NULL;
			echo '<a href="' . APP_ROOT . 'admin" class="square ' . $isActive . '"><img src="' . MEDIA . 'common/tool-admin.png" alt="" /></a>';
		}

		if (CTR::$data->get('playerInfo')->get('stepTutorial') > 0) {
			echo '<a href="#" class="hide-slpash square sh ' . (CTR::$data->get('playerInfo')->get('stepDone') ? 'active flashy' : '') . '" data-target="tutorial">';
				echo '<img src="' . MEDIA . 'common/tool-star.png" alt="tutoriel" />';
				echo '<span class="number">' . CTR::$data->get('playerInfo')->get('stepTutorial') . '</span>';
			echo '</a>';
		}
		echo '<a href="#" class="square sh" data-target="bug-tracker"><img src="' . MEDIA . 'common/tool-bugtracker.png" alt="" /></a>';

		$isActive = (in_array(CTR::getPage(), array('params'))) ? 'active' : NULL;
		echo '<a class="square hb lb ' . $isActive . '" title="paramètres" href="' . APP_ROOT . 'params"><img src="' . MEDIA . 'common/tool-param.png" alt="" /></a>';

		echo '<a href="#" class="square sh" data-target="disconnect-box"><img src="' . MEDIA . 'common/tool-exit.png" alt="" /></a>';
	echo '</div>';

	# DISPLAY OVERBOX NAV
	#####################

	# CHANGEMENT DE BASE
	echo '<div class="overbox" id="change-bases">';
		echo '<h2>Changer de bases</h2>';
		echo '<div class="overflow">';
			for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) {
				echo '<a href="' . Format::actionBuilder('switchbase', ['base' => CTR::$data->get('playerBase')->get('ob')->get($i)->get('id')]) . '" ' . (CTR::$data->get('playerBase')->get('ob')->get($i)->get('id') == CTR::$data->get('playerParams')->get('base') ? 'class="active"' : NULL) . '>';
					echo '<em>' . PlaceResource::get(CTR::$data->get('playerBase')->get('ob')->get($i)->get('type'), 'name') . '</em>';
					echo '<strong>' . CTR::$data->get('playerBase')->get('ob')->get($i)->get('name') . '</strong>';
				echo '</a>';
			}
		echo '</div>';
	echo '</div>';

	# NOTIFICATION
	echo '<div class="overbox" id="new-notifications">';
		echo '<h2>Notifications</h2>';
		if (ASM::$ntm->size() > 1) {
			echo '<a class="link-title" href="' . Format::actionBuilder('readallnotif') . '">tout marquer comme lu</a>';
		}
		echo '<div class="overflow">';
			if (ASM::$ntm->size() > 0) {
				for ($i = 0; $i < ASM::$ntm->size(); $i++) {
					$n = ASM::$ntm->get($i);
					echo '<div class="notif unreaded" data-notif-id="' . $n->getId() . '">';
						echo '<h4 class="read-notif switch-class-parent" data-class="open">' . $n->getTitle() . '</h4>';
						echo '<div class="content">' . $n->getContent() . '</div>';
						echo '<div class="footer">';
							echo '<a class="ajax-action" data-ajax-target="' . APP_ROOT . 'ajax/a-archivenotif/id-' . $n->getId() . '" href="' . Format::actionBuilder('archivenotif', ['id' => $n->getId()]) . '">archiver</a> ou ';
							echo '<a class="ajax-action" data-ajax-target="' . APP_ROOT . 'ajax/a-deletenotif/id-' . $n->getId() . '" href="' . Format::actionBuilder('deletenotif', ['id' => $n->getId()]) . '">supprimer</a><br />';
							echo '— ' . Chronos::transform($n->getDSending());
						echo '</div>';
					echo '</div>';
				}
			} else {
				echo '<p class="info">Aucune nouvelle notification.</p>';
			}
		echo '</div>';
		echo '<a href="' . APP_ROOT . 'message" class="more-link">toutes vos notifications</a>';
	echo '</div>';

	# ROADMAP
	$S_RMM_1 = ASM::$rmm->getCurrentSession();
	ASM::$rmm->newSession();
	ASM::$rmm->load(array('statement' => RoadMap::DISPLAYED), array('dCreation', 'DESC'), array(0, 10));

	echo '<div class="overbox" id="roadmap">';
		echo '<h2>Dernières mises à jour effectuées</h2>';
		echo '<div class="overflow">';
			for ($i = 0; $i < ASM::$rmm->size(); $i++) { 
				echo ($i > 0) ? '<hr />' : NULL;
				echo '<p>';
					echo '<em>' . ASM::$rmm->get($i)->dCreation. '</em>';
					echo ASM::$rmm->get($i)->pContent;
				echo '</p>';
			}
		echo '</div>';
	echo '</div>';

	ASM::$rmm->changeSession($S_RMM_1);

	# TUTORIAL
	$step = CTR::$data->get('playerInfo')->get('stepTutorial');
	
	if (CTR::$data->get('playerInfo')->get('stepTutorial') > 0) {
		echo '<div class="overbox" id="tutorial">';
			echo '<h2>Tutoriel</h2>';

			echo '<div class="overflow">';
				echo '<h3><span class="number">' . $step . '</span> ' . TutorialResource::getInfo($step, 'title') . '</h3>';
				echo '<p>' . TutorialResource::getInfo($step, 'description') . '</p>';

				echo '<p>Récompense :<br />';
				$creditReward = TutorialResource::getInfo($step, 'creditReward');
				$resourceReward = TutorialResource::getInfo($step, 'resourceReward');
				$shipReward = TutorialResource::getInfo($step, 'shipReward');

				if ($creditReward > 0) {
					echo '&nbsp;&nbsp;&nbsp;- ' . $creditReward . ' crédits<br />';
				}
				if ($resourceReward > 0) {
					echo '&nbsp;&nbsp;&nbsp;- ' . $resourceReward . ' ressources<br />';
				}
				foreach ($shipReward as $key => $value) {
					if ($value > 0) {
						echo '&nbsp;&nbsp;&nbsp;- ' . $value . ' ' . ShipResource::getInfo($key, 'codeName') . Format::plural($value) . '<br />';
					}
				}
				echo '</p>';
			echo '</div>';

			echo '<form action="' . Format::actionBuilder('validatestep') . '" method="post">';
			if (CTR::$data->get('playerInfo')->get('stepDone') == TRUE) {
				echo '<input class="outside-button" type="submit" value="valider l\'étape ' . $step . '" />';
			} else {
				echo '<input class="outside-button disabled" type="submit" value="étape en cours" disabled />';
			}
			echo '</form>';
		echo '</div>';
	}

	echo '<div class="overbox" id="bug-tracker">';
		echo '<h2>Bug tracker</h2>';
		echo '<p>Si vous trouvez des bugs ou avez des idées d\'améliorations, nous vous invitons à les poster sur le forum principal.</p>';
		echo '<a class="outside-button" target="_blank" href="' . GETOUT_ROOT . 'forum/categorie-bug" target="_blank">Reporter un bug</a>';
		echo '<a class="outside-button" target="_blank" href="' . GETOUT_ROOT . 'forum/categorie-ideas" target="_blank">Proposer une amélioration</a>';
		echo '<p>Pour les bugs que vous pensez critiques, vous pouvez envoyer un email directement à support@asylamba.com.</p>';
		echo '<a class="outside-button" href="mailto:support@asylamba.com">Envoyer un email</a>';
	echo '</div>';

	echo '<div class="overbox" id="disconnect-box">';
		echo '<a href="' . Format::actionBuilder('disconnect') . '">Se déconnecter</a>';
		echo '<hr />';
		echo '<a href="' . GETOUT_ROOT . 'profil">Changer de serveur</a>';
		echo '<hr />';
		echo '<a href="#" class="sh" data-target="roadmap">Dernières mises à jour</a>';
		echo '<hr />';
		echo '<a target="_blank" href="' . GETOUT_ROOT . 'forum">Discuter sur le forum</a>';
		echo '<a target="_blank" href="' . GETOUT_ROOT . 'blog">Voir le blog</a>';
		echo '<a target="_blank" href="' . GETOUT_ROOT . 'wiki">Consulter le wiki</a>';
		echo '<hr />';
		echo '<a target="_blank" href="' . FACEBOOK_LINK . '">Rejoindre la page Facebook</a>';
		echo '<a target="_blank" href="' . GOOGLE_PLUS_LINK . '">Nous suivre sur Google+</a>';
		echo '<a target="_blank" href="' . TWITTER_LINK . '">Nous suivre sur Twitter</a>';
	echo '</div>';
echo '</div>';

# close session
ASM::$ntm->changeSession($S_NTM1);

# open general container
echo '<div id="container">';
?>	