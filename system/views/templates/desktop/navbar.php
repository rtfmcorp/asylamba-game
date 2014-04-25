<?php
# WORK PART
###########

# load notif
include_once HERMES;
$S_NTM1 = ASM::$ntm->getCurrentSession();
ASM::$ntm->newSession();
ASM::$ntm->load(array('rPlayer' => CTR::$data->get('playerId'), 'readed' => 0));

# load message
$db = DataBase::getInstance();
$qr = $db->prepare('SELECT COUNT(id) AS n FROM message WHERE readed = 0 AND rPlayerReader = ? GROUP BY rPlayerReader');
$qr->execute(array(CTR::$data->get('playerId')));
$aw = $qr->fetch();
$message = (count($aw['n']) > 0) ? $aw['n'] : 0;


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
		for ($i = 0; $i < CTR::$data->get('playerBase')->get('ms')->size(); $i++) { 
			if (CTR::$data->get('playerParams')->get('base') == CTR::$data->get('playerBase')->get('ms')->get($i)->get('id')) {
				$currentBaseName = CTR::$data->get('playerBase')->get('ms')->get($i)->get('name');
				$currentBaseImg  = CTR::$data->get('playerBase')->get('ms')->get($i)->get('img');
				break;
			}
		}

		$isActive = (in_array(CTR::getPage(), array('bases'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'bases" class="current-base ' . $isActive . '">';
			echo '<img src="' . MEDIA . 'map/place/place' . $currentBaseImg . '.png" alt="' . $currentBaseName . '" /> ';
			echo $currentBaseName;
		echo '</a>';
	echo '</div>';

	echo '<div class="box left-2">';
		$isActive = (in_array(CTR::getPage(), array('profil'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'profil" class="square hb rb ' . $isActive . '" title="profil"><img src="' . MEDIA . 'common/nav-profil.png" alt="" /></a>';

		$isActive = (in_array(CTR::getPage(), array('fleet'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'fleet" class="square hb rb ' . $isActive . '" title="amirauté"><img src="' . MEDIA . 'common/nav-fleet.png" alt="" /></a>';

		$isActive = (in_array(CTR::getPage(), array('map'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'map" class="square hb rb ' . $isActive . '" title="carte"><img src="' . MEDIA . 'common/nav-map.png" alt="" /></a>';
		
		$isActive = (in_array(CTR::getPage(), array('financial'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'financial" class="square hb rb ' . $isActive . '" title="finance"><img src="' . MEDIA . 'common/nav-financial.png" alt="" /></a>';
	
		$isActive = (in_array(CTR::getPage(), array('technology'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'technology" class="square hb rb ' . $isActive . '" title="technologies"><img src="' . MEDIA . 'common/nav-techno.png" alt="" /></a>';
	echo '</div>';

	echo '<div class="box left-3">';
		$isActive = (in_array(CTR::getPage(), array('faction'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'faction" class="square hb rb ' . $isActive . '" title="faction"><img src="' . MEDIA . 'common/nav-faction.png" alt="" /></a>';
		
		$isActive = (in_array(CTR::getPage(), array('rank'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'rank" class="square hb rb ' . $isActive . '" title="classement"><img src="' . MEDIA . 'common/nav-rank.png" alt="" /></a>';

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

		echo '<a href="#" class="square sh" data-target="roadmap"><img src="' . MEDIA . 'common/tool-roadmap.png" alt="" /></a>';
		echo '<a href="#" class="square sh" data-target="bug-tracker"><img src="' . MEDIA . 'common/tool-bugtracker.png" alt="" /></a>';

		$isActive = (in_array(CTR::getPage(), array('params'))) ? 'active' : NULL;
		echo '<a class="square hb lb ' . $isActive . '" title="paramètres" href="' . APP_ROOT . 'params"><img src="' . MEDIA . 'common/tool-param.png" alt="" /></a>';

		echo '<a href="#" class="square sh" data-target="disconnect-box"><img src="' . MEDIA . 'common/tool-exit.png" alt="" /></a>';
	echo '</div>';

	# DISPLAY OVERBOX NAV
	#####################

	echo '<div class="overbox" id="change-bases">';
		echo '<h2>Changer de bases</h2>';
		for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) {
			echo '<a href="' . APP_ROOT . 'action/a-switchbase/base-' . CTR::$data->get('playerBase')->get('ob')->get($i)->get('id') . '" ' . (CTR::$data->get('playerBase')->get('ob')->get($i)->get('id') == CTR::$data->get('playerParams')->get('base') ? 'class="active"' : NULL) . '>';
				echo '<em>' . PlaceResource::get(CTR::$data->get('playerBase')->get('ob')->get($i)->get('type'), 'name') . '</em>';
				echo '<strong>' . CTR::$data->get('playerBase')->get('ob')->get($i)->get('name') . '</strong>';
			echo '</a>';
		}
	echo '</div>';

	echo '<div class="overbox" id="new-notifications">';
		echo '<h2>Notifications</h2>';
		echo '<div class="overflow">';
			if (ASM::$ntm->size() > 0) {
				for ($i = 0; $i < ASM::$ntm->size(); $i++) {
					$n = ASM::$ntm->get($i);
					echo '<div class="notif unreaded" data-notif-id="' . $n->getId() . '">';
						echo '<h4 class="read-notif switch-class-parent" data-class="open">' . $n->getTitle() . '</h4>';
						echo '<div class="content">' . $n->getContent() . '</div>';
						echo '<div class="footer">';
							echo '<a class="ajax-action" data-ajax-target="' . APP_ROOT . 'ajax/a-archivenotif/id-' . $n->getId() . '" href="' . APP_ROOT . 'action/a-archivenotif/id-' . $n->getId() . '">archiver</a> ou ';
							echo '<a class="ajax-action" data-ajax-target="' . APP_ROOT . 'ajax/a-deletenotif/id-' . $n->getId() . '" href="' . APP_ROOT . 'action/a-deletenotif/id-' . $n->getId() . '">supprimer</a><br />';
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

	$S_RMM_1 = ASM::$rmm->getCurrentSession();
	ASM::$rmm->newSession();
	ASM::$rmm->load(array('statement' => RoadMap::DISPLAYED), array('dCreation', 'DESC'), array(0, 10));

	echo '<div class="overbox" id="roadmap">';
		echo '<h2>Dernières mises à jour effectuées</h2>';
		echo '<div class="overflow">';
			for ($i = 0; $i < ASM::$rmm->size(); $i++) { 
				echo ($i > 0) ? '<hr />' : NULL;
				echo '<p>';
					echo '<em>' . Chronos::transform(ASM::$rmm->get($i)->dCreation). '</em>';
					echo ASM::$rmm->get($i)->pContent;
				echo '</p>';
			}
		echo '</div>';
	echo '</div>';

	ASM::$rmm->changeSession($S_RMM_1);

	include_once APOLLON;
	echo '<div class="overbox" id="bug-tracker">';
		echo '<h2>Bug tracker</h2>';
		echo '<form action="' . APP_ROOT . 'action/a-writebugreport" method="post">';
			echo '<p>Rapportez-nous vos bugs ! Il vous suffit de décrire l\'erreur rencontrée directement sur la page qui a provoquée cette dernière. ';
			echo 'Cela nous permettra de disposer du maximum d\'information.</p>';
			echo '<input type="hidden" name="url" value="' . $_SERVER['REQUEST_URI'] . '" />';
			echo '<select name="type" class="option">';
				echo '<option value="' . BugTracker::TYPE_BUG . '">bug & erreur</option>';
				echo '<option value="' . BugTracker::TYPE_ORTHOGRAPH . '">faute d\'orthographe</option>';
				echo '<option value="' . BugTracker::TYPE_DISPLAY . '">problème d\'affichage</option>';
				echo '<option value="' . BugTracker::TYPE_CALIBRATION . '">problème de calibrage (ex. prix trop élevé)</option>';
				echo '<option value="' . BugTracker::TYPE_IMPROVEMENT . '">idée d\'amélioration</option>';
			echo '</select>';
			echo '<textarea name="message" placeholder="décrivez votre erreur"></textarea>';
			echo '<input type="submit" value="envoyer" class="button" />';
		echo '</form>';
	echo '</div>';

	echo '<div class="overbox" id="disconnect-box">';
		echo '<a href="' . APP_ROOT . 'action/a-disconnect">Se déconnecter</a>';
		echo '<hr />';
		echo '<a href="' . GETOUT_ROOT . 'serveurs">Changer de serveur</a>';
		echo '<hr />';
		echo '<a target="_blank" href="' . GETOUT_ROOT . '">Aller à l\'accueil</a>';
		echo '<a target="_blank" href="' . GETOUT_ROOT . 'blog">Voir le blog</a>';
		echo '<a target="_blank" href="' . GETOUT_ROOT . 'wiki">Consulter le wiki</a>';
		echo '<hr />';
		echo '<a target="_blank" href="' . FACEBOOK_LINK . '">Rejoindre la page Facebook</a>';
		echo '<a target="_blank" href="' . TWEETER_LINK . '">Nous suivre sur Twitter</a>';
		echo '<a target="_blank" href="' . TWEETER_LINK . '">Nous suivre sur Google+</a>';
	echo '</div>';
echo '</div>';

# close session
ASM::$ntm->changeSession($S_NTM1);

# open general container
echo '<div id="container">';
?>	