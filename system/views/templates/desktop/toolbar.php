<?php
# load notif
include_once HERMES;
$S_NTM1 = ASM::$ntm->getCurrentSession();
ASM::$ntm->newSession();
ASM::$ntm->load(array('rPlayer' => CTR::$data->get('playerId'), 'readed' => 0));

echo '</div>';

echo '<div id="tools">';
	# left
	echo '<div class="box left">';
		echo '<a href="#">543 237 r</a>';
		echo '<a href="#" class="square"><img src="' . MEDIA . 'orbitalbase/generator.png" alt="" /></a>';
		echo '<a href="#" class="square"><img src="' . MEDIA . 'orbitalbase/dock1.png" alt="" /><span class="number">3</span></a>';
		echo '<a href="#" class="square"><img src="' . MEDIA . 'orbitalbase/dock2.png" alt="" /><span class="number">1</span></a>';
		echo '<a href="#" class="square"><img src="' . MEDIA . 'orbitalbase/technosphere.png" alt="" /></a>';
	echo '</div>';

	# right
	echo '<div class="temp-box right">';
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

		echo '<a href="' . APP_ROOT . 'message" class="couple ' . (($message > 0) ? 'active' : '') . '">';
			echo 'message' . Format::addPlural($message);
			echo '<strong>' . $message . '</strong>';
		echo '</a>';

		echo '<a href="' . APP_ROOT . 'message" id="general-notif-container" class="couple ' . ((ASM::$ntm->size() > 0) ? 'active' : '') . ' sh" data-target="new-notifications">';
			echo 'notification' . Format::addPlural(ASM::$ntm->size());
			echo '<strong>' . ASM::$ntm->size() . '</strong>';
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

	# overboxes
	echo '<div class="overbox" id="new-notifications">';
		echo '<div class="overflow">';
			if (ASM::$ntm->size() > 0) {
				for ($i = 0; $i < ASM::$ntm->size(); $i++) {
					$n = ASM::$ntm->get($i);
					echo '<div class="notif unreaded" data-notif-id="' . $n->getId() . '">';
						echo '<h4 class="read-notif switch-class-parent" data-class="open">' . $n->getTitle() . '</h4>';
						echo '<div class="content">' . $n->getContent() . '</div>';
						echo '<div class="footer">';
							echo '<a href="' . APP_ROOT . 'action/a-archivenotif/id-' . $n->getId() . '">archiver</a> ou ';
							echo '<a href="' . APP_ROOT . 'action/a-deletenotif/id-' . $n->getId() . '">supprimer</a><br />';
							echo '— ' . Chronos::transform($n->getDSending());
						echo '</div>';
					echo '</div>';

					if ($i == NTM_TOOLDISPLAY - 1) {
						break;
					}
				}
				echo '<a href="' . APP_ROOT . 'message" class="more-link">voir toutes vos notifications</a>';
			} else {
				echo '<a href="' . APP_ROOT . 'message" class="more-link">aucune nouvelle notification</a>';
			}
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$ntm->changeSession($S_NTM1);
?>