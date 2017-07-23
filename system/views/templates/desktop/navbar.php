<?php

use Asylamba\Classes\Library\Format;
use Asylamba\Classes\Container\Params;
use Asylamba\Modules\Gaia\Resource\PlaceResource;
use Asylamba\Classes\Library\Chronos;
use Asylamba\Modules\Hermes\Model\RoadMap;
use Asylamba\Modules\Athena\Resource\ShipResource;
use Asylamba\Modules\Zeus\Resource\TutorialResource;

$session = $this->getContainer()->get('session_wrapper');
$notificationManager = $this->getContainer()->get('hermes.notification_manager');
$roadmapManager = $this->getContainer()->get('hermes.roadmap_manager');
$database = $this->getContainer()->get('database');
$request = $this->getContainer()->get('app.request');
$response = $this->getContainer()->get('app.response');
$sessionToken = $session->get('token');

$notifications = $notificationManager->getUnreadNotifications($session->get('playerId'));
$nbNotifications = count($notifications);
# load message
$qr = $database->prepare('SELECT COUNT(c.id) AS count
	FROM `conversation` AS c
	LEFT JOIN `conversationUser` AS u
		ON u.rConversation = c.id
	WHERE u.rPlayer = ?
	AND u.dLastView < c.dLastMessage'
);
$qr->execute(array($session->get('playerId')));
$message = $qr->fetch();
$message = $message['count'];
$qr->closeCursor();

# DISPLAY NAV BAR
#################
echo '<div id="nav">';
	echo '<div class="box left">';
		echo '<a href="#" class="square sh" data-target="change-bases"><img src="' . MEDIA . 'common/nav-base.png" alt="" /></a>';

		# select current base name
		$currentBaseName = NULL;
		$currentBaseImg  = NULL;
		for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
			if ($session->get('playerParams')->get('base') == $session->get('playerBase')->get('ob')->get($i)->get('id')) {
				$currentBaseName = $session->get('playerBase')->get('ob')->get($i)->get('name');
				$currentBaseImg  = $session->get('playerBase')->get('ob')->get($i)->get('img');
				break;
			}
		}

		if ($session->get('playerBase')->get('ob')->get(0)) {
			$nextBaseId = $session->get('playerBase')->get('ob')->get(0)->get('id');
			$finded = FALSE;
			for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
				if ($finded) {
					$nextBaseId = $session->get('playerBase')->get('ob')->get($i)->get('id');
					break;
				}
				if ($session->get('playerParams')->get('base') == $session->get('playerBase')->get('ob')->get($i)->get('id')) {
					$finded = TRUE;
				}
			}
		} else {
			$nextBaseId = 0;
			$currentBaseName = 'Reconnectez-vous';
			$currentBaseImg = '1-1';
		}

		$isActive = (in_array($response->getPage(), array('bases'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'bases" class="current-base ' . $isActive . '">';
			echo '<img src="' . MEDIA . 'map/place/place' . $currentBaseImg . '.png" alt="' . $currentBaseName . '" /> ';
			echo $currentBaseName;
		echo '</a>';
		echo '<a href="' . Format::actionBuilder('switchbase', $sessionToken, ['base' => $nextBaseId]) . '" class="square">';
			echo '<img src="' . MEDIA . 'common/next-base.png" alt="base suivante" />';
		echo '</a>';
	echo '</div>';

	echo '<div class="box left-2">';
		$isActive = (in_array($response->getPage(), array('profil'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'profil" class="square hb rb ' . $isActive . '" title="profil"><img src="' . MEDIA . 'common/nav-profil.png" alt="" /></a>';

		$isActive = (in_array($response->getPage(), array('fleet'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'fleet" class="square hb rb ' . $isActive . '" title="amirauté"><img src="' . MEDIA . 'common/nav-fleet.png" alt="" /></a>';

		$isActive = (in_array($response->getPage(), array('map'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'map" class="square hb rb ' . $isActive . '" title="galaxie"><img src="' . MEDIA . 'common/nav-map.png" alt="" /></a>';
		
		$isActive = (in_array($response->getPage(), array('financial'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'financial" class="square hb rb ' . $isActive . '" title="finances"><img src="' . MEDIA . 'common/nav-financial.png" alt="" /></a>';
	
		$isActive = (in_array($response->getPage(), array('technology'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'technology" class="square hb rb ' . $isActive . '" title="université"><img src="' . MEDIA . 'common/nav-techno.png" alt="" /></a>';
		
		$isActive = (in_array($response->getPage(), array('faction'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'faction" class="square hb rb ' . $isActive . '" title="faction"><img src="' . MEDIA . 'common/nav-faction.png" alt="" /></a>';
	echo '</div>';

	echo '<div class="box left-3">';
		$isActive = (in_array($response->getPage(), array('rank'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'rank" class="square hb rb ' . $isActive . '" title="classements"><img src="' . MEDIA . 'common/nav-rank.png" alt="" /></a>';

		$isActive = (in_array($response->getPage(), array('embassy'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'embassy" class="square hb rb ' . $isActive . '" title="ambassades"><img src="' . MEDIA . 'common/nav-embassy.png" alt="" /></a>';

		$isActive = (in_array($response->getPage(), array('message'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'message" class="square hb rb ' . $isActive . '" title="messagerie"><img src="' . MEDIA . 'common/nav-message.png" alt="" />';
			echo ($message > 0) ? '<span class="number">' . $message . '</span>' : NULL;
		echo '</a>';

		echo '<a href="' . APP_ROOT . 'message" id="general-notif-container" class="square sh" data-target="new-notifications"><img src="' . MEDIA . 'common/nav-notif.png" alt="" />';
			echo ($nbNotifications > 0) ? '<span class="number">' . $nbNotifications . '</span>' : NULL;
		echo '</a>';
	echo '</div>';

	echo '<div class="box right">';
		if ($session->get('playerInfo')->get('admin') == TRUE) {
			$isActive = (in_array($response->getPage(), array('admin'))) ? 'active' : NULL;
			echo '<a href="' . APP_ROOT . 'admin" class="square ' . $isActive . '"><img src="' . MEDIA . 'common/tool-admin.png" alt="" /></a>';
		}

		echo '<a ' . ((bool)$request->cookies->get('p' . Params::REDIRECT_CHAT, Params::$params[Params::REDIRECT_CHAT]) ? 'href="https://discordapp.com/channels/132106417703354378/132106417703354378" target="_blank"' : 'href="' . APP_ROOT . 'params"') . '" class="square"><img src="' . MEDIA . 'common/nav-chat.png" alt="" /></a>';

		if ($session->get('playerInfo')->get('stepTutorial') > 0) {
			echo '<a href="#" class="hide-slpash square sh ' . ($session->get('playerInfo')->get('stepDone') ? 'active flashy' : '') . '" data-target="tutorial">';
				echo '<img src="' . MEDIA . 'common/tool-star.png" alt="tutoriel" />';
				echo '<span class="number">' . $session->get('playerInfo')->get('stepTutorial') . '</span>';
			echo '</a>';
		}
		echo '<a href="#" class="square sh" data-target="bug-tracker"><img src="' . MEDIA . 'common/tool-bugtracker.png" alt="" /></a>';

		$isActive = (in_array($response->getPage(), array('params'))) ? 'active' : NULL;
		echo '<a class="square hb lb ' . $isActive . '" title="paramètres" href="' . APP_ROOT . 'params"><img src="' . MEDIA . 'common/tool-param.png" alt="" /></a>';

		echo '<a href="#" class="square sh" data-target="disconnect-box"><img src="' . MEDIA . 'common/tool-exit.png" alt="" /></a>';
	echo '</div>';

	# DISPLAY OVERBOX NAV
	#####################

	# CHANGEMENT DE BASE
	echo '<div class="overbox" id="change-bases">';
		echo '<h2>Changer de bases</h2>';
		echo '<div class="overflow">';
			for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) {
				echo '<a href="' . Format::actionBuilder('switchbase', $sessionToken, ['base' => $session->get('playerBase')->get('ob')->get($i)->get('id')]) . '" ' . ($session->get('playerBase')->get('ob')->get($i)->get('id') == $session->get('playerParams')->get('base') ? 'class="active"' : NULL) . '>';
					echo '<em>' . PlaceResource::get($session->get('playerBase')->get('ob')->get($i)->get('type'), 'name') . '</em>';
					echo '<strong>' . $session->get('playerBase')->get('ob')->get($i)->get('name') . '</strong>';
				echo '</a>';
			}
		echo '</div>';
	echo '</div>';

	# NOTIFICATION
	echo '<div class="overbox" id="new-notifications">';
		echo '<h2>Notifications</h2>';
		if ($nbNotifications > 1) {
			echo '<a class="link-title" href="' . Format::actionBuilder('readallnotif', $sessionToken) . '">tout marquer comme lu</a>';
		}
		echo '<div class="overflow">';
			if ($nbNotifications > 0) {
				foreach ($notifications as $n) {
					echo '<div class="notif unreaded" data-notif-id="' . $n->getId() . '">';
						echo '<h4 class="read-notif switch-class-parent" data-class="open">' . $n->getTitle() . '</h4>';
						echo '<div class="content">' . $n->getContent() . '</div>';
						echo '<div class="footer">';
							echo '<a class="ajax-action" data-ajax-target="' . APP_ROOT . 'ajax/a-archivenotif/id-' . $n->getId() . '" href="' . Format::actionBuilder('archivenotif', $sessionToken, ['id' => $n->getId()]) . '">archiver</a> ou ';
							echo '<a class="ajax-action" data-ajax-target="' . APP_ROOT . 'ajax/a-deletenotif/id-' . $n->getId() . '" href="' . Format::actionBuilder('deletenotif', $sessionToken, ['id' => $n->getId()]) . '">supprimer</a><br />';
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
	$S_RMM_1 = $roadmapManager->getCurrentSession();
	$roadmapManager->newSession();
	$roadmapManager->load(array('statement' => RoadMap::DISPLAYED), array('dCreation', 'DESC'), array(0, 10));

	echo '<div class="overbox" id="roadmap">';
		echo '<h2>Dernières mises à jour effectuées</h2>';
		echo '<div class="overflow">';
			for ($i = 0; $i < $roadmapManager->size(); $i++) { 
				echo ($i > 0) ? '<hr />' : NULL;
				echo '<p>';
					echo '<em>' . $roadmapManager->get($i)->dCreation. '</em>';
					echo $roadmapManager->get($i)->pContent;
				echo '</p>';
			}
		echo '</div>';
	echo '</div>';

	$roadmapManager->changeSession($S_RMM_1);

	# TUTORIAL
	$step = $session->get('playerInfo')->get('stepTutorial');
	
	if ($session->get('playerInfo')->get('stepTutorial') > 0) {
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

			echo '<form action="' . Format::actionBuilder('validatestep', $sessionToken) . '" method="post">';
			if ($session->get('playerInfo')->get('stepDone') == TRUE) {
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
		echo '<a class="outside-button" target="_blank" href="' . $this->getContainer()->getParameter('getout_root') . 'forum/categorie-bug" target="_blank">Reporter un bug</a>';
		echo '<a class="outside-button" target="_blank" href="' . $this->getContainer()->getParameter('getout_root') . 'forum/categorie-opensource" target="_blank">Proposer une amélioration</a>';
		echo '<p>Pour les bugs que vous pensez critiques, vous pouvez envoyer un email directement à support@asylamba.com.</p>';
		echo '<a class="outside-button" href="mailto:support@asylamba.com">Envoyer un email</a>';
	echo '</div>';

	echo '<div class="overbox" id="disconnect-box">';
		echo '<a href="' . Format::actionBuilder('disconnect', $sessionToken) . '">Se déconnecter</a>';
		echo '<hr />';
		echo '<a href="#" class="sh" data-target="roadmap">Dernières mises à jour</a>';
		echo '<a href="' . APP_ROOT . 'sponsorship">Parrainage</a>';
		echo '<hr />';
		echo '<a target="_blank" href="' . $this->getContainer()->getParameter('getout_root') . 'forum">Discuter sur le forum</a>';
		echo '<a target="_blank" href="' . $this->getContainer()->getParameter('getout_root') . 'wiki">Consulter le wiki</a>';
		echo '<hr />';
		echo '<a target="_blank" href="' . FACEBOOK_LINK . '">Rejoindre la page Facebook</a>';
		echo '<a target="_blank" href="' . GOOGLE_PLUS_LINK . '">Nous suivre sur Google+</a>';
		echo '<a target="_blank" href="' . TWITTER_LINK . '">Nous suivre sur Twitter</a>';
	echo '</div>';
echo '</div>';

# open general container
echo '<div id="container">';