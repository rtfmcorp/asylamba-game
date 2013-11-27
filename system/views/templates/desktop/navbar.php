<?php
echo '<div id="nav">';
	echo '<div class="box left">';
		$isActive = (in_array(CTR::getPage(), array('profil', 'message', 'fleet', 'financial', 'technology', 'spying'))) ? 'class="active"' : '';
		echo '<a href="' . APP_ROOT . 'profil" ' . $isActive . '>Domaine</a>';

		$isActive = (in_array(CTR::getPage(), array('bases', 'base'))) ? 'class="active"' : '';
		echo '<a href="' . APP_ROOT . 'bases" ' . $isActive . '>Bases</a>';

		$isActive = (in_array(CTR::getPage(), array('map'))) ? 'class="active"' : '';
		echo '<a href="' . APP_ROOT . 'map" ' . $isActive . '>Carte</a>';

		$isActive = (in_array(CTR::getPage(), array('faction', 'forum', 'election'))) ? 'class="active"' : '';
		echo '<a href="' . APP_ROOT . 'faction" ' . $isActive . '>Faction</a>';
	echo '</div>';

	echo '<div class="box right">';
		if (CTR::$data->get('playerInfo')->get('admin') == TRUE) {
			$isActive = (in_array(CTR::getPage(), array('admin'))) ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'admin" class="square ' . $isActive . '"><img src="' . MEDIA . 'common/tool-admin.png" alt="" /></a>';
		}

		echo '<a href="#" class="square sh" data-target="roadmap"><img src="' . MEDIA . 'common/tool-roadmap.png" alt="" /></a>';
		echo '<a href="#" class="square sh" data-target="bug-tracker"><img src="' . MEDIA . 'common/tool-bugtracker.png" alt="" /></a>';

		//$isActive = (in_array(CTR::getPage(), array('params'))) ? 'class="active"' : '';
		//echo '<a href="' . APP_ROOT . 'params" ' . $isActive . '>Paramètres</a>';
		echo '<a href="#" class="square hb lb" title="en construction"><img src="' . MEDIA . 'common/tool-param.png" alt="" /></a>';
		echo '<a href="#" class="square sh" data-target="disconnect-box"><img src="' . MEDIA . 'common/tool-exit.png" alt="" /></a>';
	echo '</div>';

	$S_RMM_1 = ASM::$rmm->getCurrentSession();
	ASM::$rmm->newSession();
	ASM::$rmm->load(array('statement' => RoadMap::DISPLAYED), array('dCreation', 'DESC'), array(0, 10));

	echo '<div class="roadmap" id="roadmap">';
		echo '<div class="overflow">';
			echo '<p>Dernières mises à jour effectuées.</p>';
			for ($i = 0; $i < ASM::$rmm->size(); $i++) { 
				echo '<hr />';
				echo '<p>';
					echo '<em>' . Chronos::transform(ASM::$rmm->get($i)->dCreation). '</em>';
					echo ASM::$rmm->get($i)->pContent;
				echo '</p>';
			}
		echo '</div>';
	echo '</div>';

	ASM::$rmm->changeSession($S_RMM_1);

	include_once APOLLON;
	echo '<div class="bug-tracker" id="bug-tracker">';
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
		echo '<a target="_blank" href="' . TWEETER_LINK . '">Nous suivre sur Tweeter</a>';
		echo '<a target="_blank" href="' . TWEETER_LINK . '">Nous suivre sur Google+</a>';
	echo '</div>';
echo '</div>';

echo '<div id="container">';
?>	