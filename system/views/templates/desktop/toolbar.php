<?php
echo '</div>';

echo '<div id="tools">';
	echo '<div class="box right">';
		echo '<a href="#" class="couple hb lt" title="temps avant prochaine relève">';
				echo 'il reste <span class="releve-timer">' . Chronos::getTimer('i') . ':' . Chronos::getTimer('s') . '</span>';
		echo '</a>';

		if (isset($base) && CTR::getPage() == 'bases') {
			echo '<a href="' . APP_ROOT . 'bases/base-' . $base->getId() . '/view-refinery" class="couple hb lt" title="ressources dans vos dépots sur ' . $base->getName() . '">';
				echo '<strong>';
					echo Format::numberFormat($base->getResourcesStorage());
					echo ' <img class="icon-color" src="' . MEDIA . 'resources/resource.png" alt="ressources" />';
				echo '</strong>';
			echo '</a>';
		}

		echo '<a href="' . APP_ROOT . 'financial" class="couple hb lt" title="crédits à votre disposition">';
			echo '<strong>';
				echo Format::numberFormat(CTR::$data->get('playerInfo')->get('credit'));
				echo ' <img class="icon-color" src="' . MEDIA . 'resources/credit.png" alt="crédits" />';
			echo '</strong>';
		echo '</a>';
		echo '<a href="' . APP_ROOT . 'fleet" class="couple hb lt" title="points d\'attaque à votre disposition">';
			echo '<strong>';
				echo CTR::$data->get('playerInfo')->get('actionPoint');
				echo ' <img class="icon-color" src="' . MEDIA . 'resources/pa.png" alt="points d\'attaque" />';
			echo '</strong>';
		echo '</a>';

		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT COUNT(id) AS n FROM message WHERE readed = 0 AND rPlayerReader = ? GROUP BY rPlayerReader');
		$qr->execute(array(CTR::$data->get('playerId')));
		$aw = $qr->fetch();
		$message = (count($aw['n']) > 0) ? $aw['n'] : 0;
		echo '<a href="' . APP_ROOT . 'message" class="couple ' . (($message > 0) ? 'active' : '') . ' hb lt" title="' . (($message > 0) ? 'vous avez ' . $message . ' nouveau' . Format::addPlural($message, 'x') . ' message' . Format::addPlural($message) : 'vous n\'avez pas de nouveau message') . '">';
			echo 'message' . Format::addPlural($message);
			echo '<strong>' . $message . '</strong>';
		echo '</a>';

		include_once HERMES;
		$S_NTM1 = ASM::$ntm->getCurrentSession();
		ASM::$ntm->newSession();
		ASM::$ntm->load(array('rPlayer' => CTR::$data->get('playerId'), 'readed' => 0));
		$notifs = ASM::$ntm->size();
		ASM::$ntm->changeSession($S_NTM1);
		echo '<a href="' . APP_ROOT . 'message" id="general-notif-container" class="couple ' . (($notifs > 0) ? 'active' : '') . ' hb lt" title="' . (($notifs > 0) ? 'vous avez ' . $notifs . ' nouvelle' . Format::addPlural($notifs) . ' notification' . Format::addPlural($notifs) : 'vous n\'avez pas de nouvelle notification') . '">';
			echo 'notification' . Format::addPlural($notifs);
			echo '<strong>' . $notifs . '</strong>';
		echo '</a>';

		$incomingAttack = 0;
		for ($i = 0; $i < CTR::$data->get('playerEvent')->size(); $i++) {
			if (CTR::$data->get('playerEvent')->get($i)->get('eventType') == EVENT_INCOMING_ATTACK) {
				$info = CTR::$data->get('playerEvent')->get($i)->get('eventInfo');
				if ($info[0] === TRUE) { $incomingAttack++; }
			}
		}
		if ($incomingAttack > 0) {
			echo '<a href="' . APP_ROOT . 'fleet" class="active couple hb lt" title="' . $incomingAttack . ' attaque' . Format::addPlural($incomingAttack) . ' entrante' . Format::addPlural($incomingAttack) . '">';
				echo '<strong>';
					echo $incomingAttack;
					echo ' <img class="icon-color" src="' . MEDIA . 'resources/attack.png" alt="points d\'attaque" />';
				echo '</strong>';
			echo '</a>';
		}
	echo '</div>';
echo '</div>';
?>