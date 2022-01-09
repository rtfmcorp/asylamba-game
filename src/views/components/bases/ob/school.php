<?php
# refinery component
# in athena.bases package

# affichage de la raffinerie

# require
	# {orbitalBase}		ob_school

use App\Classes\Library\Format;
use App\Modules\Zeus\Helper\CheckName;
use App\Modules\Ares\Model\Commander;
use App\Modules\Zeus\Model\PlayerBonus;
use App\Modules\Athena\Resource\SchoolClassResource;
use App\Modules\Ares\Resource\CommanderResources;
use App\Modules\Gaia\Resource\PlaceResource;

$container = $this->getContainer();
$appRoot = $container->getParameter('app_root');
$mediaPath = $container->getParameter('media');
$commanderManager = $this->getContainer()->get(\App\Modules\Ares\Manager\CommanderManager::class);
$session = $this->getContainer()->get(\App\Classes\Library\Session\SessionWrapper::class);
$sessionToken = $session->get('token');

$commanders = $commanderManager->getBaseCommanders($ob_school->getId(), [Commander::INSCHOOL], ['c.experience' => 'DESC']);

# max commander
$maxCommanderInSchool = PlaceResource::get($ob_school->typeOfBase, 'school-size');

# gain en crédit
$invest  = $ob_school->iSchool;
$invest += $invest * $session->get('playerBonus')->get(PlayerBonus::COMMANDER_INVEST) / 100;
$earnedExperience  = $invest / Commander::COEFFSCHOOL;
$earnedExperience  = round($earnedExperience);
$earnedExperience  = ($earnedExperience < 0)
	? 0 : $earnedExperience;

echo '<div class="component school">';
	echo '<div class="head skin-1">';
		echo '<img src="' . $mediaPath . 'orbitalbase/school.png" alt="" />';
		echo '<h2>Ecole de Cmd.</h2>';
		echo '<em>Formation des officiers</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="number-box">';
				echo '<span class="label">investissements alloués à l\'école</span>';
				echo '<span class="value">';
					echo Format::numberFormat($ob_school->iSchool);
					echo ' <img alt="crédits" src="' . $mediaPath . 'resources/credit.png" class="icon-color">';
				echo '</span>';
				echo '<span class="group-link">';
					echo '<a title="modifier" class="hb lt" href="' . $appRoot . 'financial/sftr-4">→</a>';
				echo '</span>';
			echo '</div>';

			echo $session->get('playerBonus')->get(PlayerBonus::COMMANDER_INVEST) == 0
				? '<div class="number-box grey">'
				: '<div class="number-box">';
				echo '<span class="label">bonus de formation</span>';
				echo '<span class="value">';
					echo $session->get('playerBonus')->get(PlayerBonus::COMMANDER_INVEST) . ' %';
				echo '</span>';
			echo '</div>';

			echo '<hr />';

			echo '<form action="' . Format::actionBuilder('createschoolclass', $sessionToken, ['baseid' => $ob_school->getId(), 'school' => '0']) . '" method="post" class="build-item">';
				echo '<div class="name">';
					echo '<img src="' . $mediaPath . 'school/school-1.png" alt="" />';
					echo '<strong>Former un nouvel officier</strong>';
				echo '</div>';
					echo '<input type="text" class="name-commander" name="name" value="' . CheckName::randomize() . '" />';
				if (count($commanders) >= $maxCommanderInSchool) {
					echo '<span class="button disable">';
						echo '<span class="text">';
							echo 'trop d\'officiers dans l\'école<br/>';
							echo Format::numberFormat(SchoolClassResource::getInfo(0, 'credit')) . ' <img src="' .  $mediaPath. 'resources/credit.png" alt="crédits" class="icon-color" />';
						echo '</span>';
					echo '</span>';
				} elseif (SchoolClassResource::getInfo(0, 'credit') > $session->get('playerInfo')->get('credit')) {
					echo '<span class="button disable">';
						echo '<span class="text">';
							echo 'vous ne disposez pas d\'assez de crédit<br/>';
							echo Format::numberFormat(SchoolClassResource::getInfo(0, 'credit')) . ' <img src="' .  $mediaPath. 'resources/credit.png" alt="crédits" class="icon-color" />';
						echo '</span>';
					echo '</span>';
				} else {
					echo '<button type="submit" class="button">';
						echo '<span class="text">';
							echo 'créer l\'officier pour<br/>';
							echo Format::numberFormat(SchoolClassResource::getInfo(0, 'credit')) . ' <img src="' .  $mediaPath. 'resources/credit.png" alt="crédits" class="icon-color" />';
						echo '</span>';
					echo '</button>';
				}
			echo '</form>';
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component">';
	echo '<div class="head skin-5">';
		echo '<h2>Salle de formation</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="queue">';
				for ($i = 0; $i < $maxCommanderInSchool; $i++) {
					if (isset($commanders[$i])) {
						$commander = $commanders[$i];
						$expToLvlUp = $commanderManager->experienceToLevelUp($commander);
						echo '<div class="item">';
							echo '<img class="picto" src="' . $mediaPath . 'commander/small/' . $commander->avatar . '.png" alt="" />';
							echo '<strong>' . CommanderResources::getInfo($commander->level, 'grade') . ' ' . $commander->getName() . '</strong>';
							echo '<em>' . Format::numberFormat($commander->getExperience()) . ' points d\'expérience</em>';
							echo '<em>~ ' . Format::number($earnedExperience) . 'xp/relève</em>';
							echo '<span class="group-link">';
								echo '<a class="hb lt" title="affecter l\'officier" href="' . Format::actionBuilder('affectcommander', $sessionToken, ['id' => $commander->getId()]) . '">&#8593;</a>';
								echo '<a class="hb lt" title="placer l\'officier dans le mess" href="' . Format::actionBuilder('putcommanderinschool', $sessionToken, ['id' => $commander->getId()]) . '">&#8594;</a>';
							echo '</span>';
							echo '<span class="progress-container">';
								echo '<span style="width: ' . Format::percent($commander->getExperience() - ($expToLvlUp / 2), $expToLvlUp - ($expToLvlUp / 2)) . '%;" class="progress-bar"></span>';
							echo '</span>';
						echo '</div>';
					} else {
						echo '<div class="item empty">';
							echo '<span class="picto"></span>';
							echo '<strong>Emplacement libre</strong>';
							echo '<span class="progress-container"></span>';
						echo '</div>';
					}
				}
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

$reserveCommanders = $commanderManager->getBaseCommanders($ob_school->getId(), ['c.statement' => Commander::RESERVE], ['c.experience' => 'DESC']);

echo '<div class="component">';
	echo '<div class="head skin-5">';
		echo '<h2>Mess des officiers</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="queue">';
				foreach ($reserveCommanders as $commander) {
					$expToLvlUp = $commanderManager->experienceToLevelUp($commander);
					echo '<div class="item">';
						echo '<img class="picto" src="' . $mediaPath . 'commander/small/' . $commander->avatar . '.png" alt="" />';
						echo '<strong>' . CommanderResources::getInfo($commander->level, 'grade') . ' ' . $commander->getName() . '</strong>';
						echo '<em>' . Format::numberFormat($commander->getExperience()) . ' points d\'expérience</em>';
						echo '<span class="group-link">';
							echo '<a class="hb lt" title="affecter l\'officier" href="' . Format::actionBuilder('affectcommander', $sessionToken, ['id' => $commander->getId()]) . '">&#8593;</a>';
							echo '<a class="hb lt" title="placer l\'officier dans l\'école" href="' . Format::actionBuilder('putcommanderinschool', $sessionToken, ['id' => $commander->getId()]) . '">&#8592;</a>';
						echo '</span>';
						echo '<span class="progress-container">';
							echo '<span style="width: ' . Format::percent($commander->getExperience() - ($expToLvlUp / 2), $expToLvlUp - ($expToLvlUp / 2)) . '%;" class="progress-bar"></span>';
						echo '</span>';
					echo '</div>';
				}

				echo '<div class="item empty">';
					echo '<span class="picto"></span>';
					echo '<strong>Emplacement libre</strong>';
					echo '<span class="progress-container"></span>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>À propos</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<p class="long-info">L’<strong>Ecole de commandement</strong> est le centre de formation de vos officiers. Il suffit pour cela d’engager de jeunes recrues sans expérience et d’investir un peu d’argent dans l’école pour que de brillants stratèges issus de toute la galaxie viennent enseigner leur savoir aux jeunes commandants de votre école.<br /><br />
			Au fil du temps ils gagneront de l’<strong>expérience et des niveaux</strong>. En gradant, ils acquerront la capacité de diriger une escadrille supplémentaire, ce qui augmentera la taille maximale de la flotte qu’ils dirigeront. L’investissement dans l’école de commandement s’applique à tout les commandants en formation.<br /><br />
			Le nombre de place de formation est limité. Il est cepandant plus élevé sur les <strong>bases militaires</strong> et les <strong>capitales</strong>. Le mess des officiers permet de mettre vos officiers au repos.</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';
