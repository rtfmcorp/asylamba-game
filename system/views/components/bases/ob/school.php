<?php
# refinery component
# in athena.bases package

# affichage de la raffinerie

# require
	# {orbitalBase}		ob_school

include_once ARES;
include_once ZEUS;

$S_COM1 = ASM::$com->getCurrentSession();
ASM::$com->newSession();
ASM::$com->load(array('c.statement' => Commander::INSCHOOL, 'c.rBase' => $ob_school->getId()), array('c.experience', 'DESC'));
$comQuantity = ASM::$com->size();

echo '<div class="component school">';
	echo '<div class="head skin-1">';
		echo '<img src="' . MEDIA . 'orbitalbase/school.png" alt="" />';
		echo '<h2>Ecole de Cmd.</h2>';
		echo '<em>formation des officiers</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="number-box">';
				echo '<span class="label">investissements alloués à l\'école</span>';
				echo '<span class="value">';
					echo Format::numberFormat($ob_school->getISchool());
					echo ' <img alt="crédits" src="' . MEDIA . 'resources/credit.png" class="icon-color">';
				echo '</span>';
				echo '<span class="group-link">';
					echo '<a title="modifier" class="hb lt" href="' . APP_ROOT . 'financial/sftr-4">→</a>';
				echo '</span>';
			echo '</div>';

			if (CTR::$data->get('playerBonus')->get(PlayerBonus::COMMANDER_INVEST) == 0) {
				echo '<div class="number-box grey">';
			} else {
				echo '<div class="number-box">';
			}
				echo '<span class="label">bonus de formation</span>';
				echo '<span class="value">';
					echo CTR::$data->get('playerBonus')->get(PlayerBonus::COMMANDER_INVEST) . ' %';
				echo '</span>';
			echo '</div>';

			echo '<hr />';

			echo '<form action="' . Format::actionBuilder('createschoolclass', ['baseid' => $ob_school->getId(), 'school' => '0']) . '" method="post" class="build-item">';
				echo '<div class="name">';
					echo '<img src="' . MEDIA . 'school/school-1.png" alt="" />';
					echo '<strong>Nouvel officier</strong>';
				echo '</div>';
					echo '<input type="text" class="name-commander" name="name" value="' . CheckName::randomize() . '" />';
				if ($comQuantity >= MAXCOMMANDERINSCHOOL) {
					echo '<span class="button disable">';
						echo '<span class="text">';
							echo 'trop d\'officier dans l\'école<br/>';
							echo Format::numberFormat(SchoolClassResource::getInfo(0, 'credit')) . ' <img src="' .  MEDIA. 'resources/credit.png" alt="crédits" class="icon-color" />';
						echo '</span>';
					echo '</span>';
				} elseif (SchoolClassResource::getInfo(0, 'credit') > CTR::$data->get('playerInfo')->get('credit')) {
					echo '<span class="button disable">';
						echo '<span class="text">';
							echo 'vous ne disposez pas d\'assez de crédit<br/>';
							echo Format::numberFormat(SchoolClassResource::getInfo(0, 'credit')) . ' <img src="' .  MEDIA. 'resources/credit.png" alt="crédits" class="icon-color" />';
						echo '</span>';
					echo '</span>';
				} else {
					echo '<button type="submit" class="button">';
						echo '<span class="text">';
							echo 'créer l\'officier pour<br/>';
							echo Format::numberFormat(SchoolClassResource::getInfo(0, 'credit')) . ' <img src="' .  MEDIA. 'resources/credit.png" alt="crédits" class="icon-color" />';
						echo '</span>';
					echo '</button>';
				}
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>Salle de formation</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="queue">';
				for ($i = 0; $i < ASM::$com->size(); $i++) {
					$commander = ASM::$com->get($i);
					$expToLvlUp = $commander->experienceToLevelUp();
					echo '<div class="item">';
						echo '<img class="picto" src="' . MEDIA . 'commander/small/' . $commander->avatar . '.png" alt="" />';
						echo '<strong>' . CommanderResources::getInfo($commander->level, 'grade') . ' ' . $commander->getName() . '</strong>';
						echo '<em>' . Format::numberFormat($commander->getExperience()) . ' points d\'expérience</em>';
						echo '<a href="' . Format::actionBuilder('affectcommander', ['id' => $commander->getId()]) . '">';
							echo 'affecter';
						echo '</a>';
						echo '<span class="progress-container">';
							echo '<span style="width: ' . Format::percent($commander->getExperience() - ($expToLvlUp / 2), $expToLvlUp - ($expToLvlUp / 2)) . '%;" class="progress-bar"></span>';
						echo '</span>';
					echo '</div>';
				}

				if (ASM::$com->size() == 0) {
					echo '<em>Classes vides, aucun commandant en formation.</em>';
				}
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

ASM::$com->changeSession($S_COM1);

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>À propos</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<p class="long-info">L’<strong>Ecole de commandement</strong> est le centre de formation de vos commandants. Il suffit pour cela d’ouvrir des classes et d’investir un peu d’argent dans l’école pour que de brillants commandants issus de toute la galaxie viennent enseigner leur savoir aux jeunes commandants de votre école.<br /><br />Au fil du temps ils gagneront de l’expérience et des niveaux. En gradant, ils acquerront la capacité de diriger une escadrille supplémentaire, ce qui augmente la taille maximale de vos flottes.<br /><br />Lorsque vous jugerez qu’un de vos commandants est assez formé, il vous suffit de l’affecter. Il sera alors à même de diriger sa flotte et de prendre part à votre guerre expansionniste.</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>