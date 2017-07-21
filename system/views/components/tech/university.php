<?php
# generator component
# in athena.bases package

# affichage de l'université

# require
	# {player}		player_university

# work

use Asylamba\Classes\Library\Format;
use Asylamba\Modules\Zeus\Model\PlayerBonus;
use Asylamba\Modules\Promethee\Resource\ResearchResource;
use Asylamba\Modules\Promethee\Model\Research;

$session = $this->getContainer()->get('session_wrapper');
$researchHelper = $this->getContainer()->get('promethee.research_helper');

echo '<div class="component">';
	echo '<div class="head skin-1">';
		echo '<img src="' . MEDIA . 'orbitalbase/university.png" alt="" />';
		echo '<h2>Université</h2>';
		echo '<em>recherche & développement</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="number-box" id="uni-total-invest" data-invest="' . $player_university->iUniversity . '" data-baseid="' . $player_university->getId() . '">';
				echo '<span class="label">investissements alloués à l\'université</span>';
				echo '<span class="value">';
					echo Format::numberFormat($player_university->iUniversity);
					echo ' <img alt="crédit" src="' . MEDIA . 'resources/credit.png" class="icon-color">';
				echo '</span>';
				echo '<span class="group-link"><a title="modifier" class="hb lt" href="' . APP_ROOT . 'financial/">→</a></span>';
			echo '</div>';

			echo $session->get('playerBonus')->get(PlayerBonus::UNI_INVEST) == 0
				? '<div class="number-box grey">'
				: '<div class="number-box">';
				echo '<span class="label">bonus d\'efficacité d\'investissement</span>';
				echo '<span class="value">';
					echo $session->get('playerBonus')->get(PlayerBonus::UNI_INVEST) . ' %';
				echo '</span>';
			echo '</div>';

			echo '<hr />';

			echo '<div class="number-box" id="uni-percent-natural" data-percent="' . $player_university->partNaturalSciences . '">';
				echo '<span class="label">sciences naturelles</span>';
				echo '<span class="value">';
					echo '<span class="uni-value">' . $player_university->partNaturalSciences . '</span> %';
				echo '</span>';
				echo '<span class="progress-bar">';
					echo '<span style="width:' . $player_university->partNaturalSciences . '%;" class="content"></span>';
				echo '</span>';
				echo '<span class="group-link">';
					echo '<a href="#" class="uni-invest-button decrease" data-type="natural" />-</a>';
					echo '<a href="#" class="uni-invest-button increase" data-type="natural" />+</a>';
				echo '</span>';
			echo '</div>';
			echo '<div class="number-box" id="uni-percent-informatic" data-percent="' . $player_university->partInformaticEngineering . '">';
				echo '<span class="label">ingénierie informatique</span>';
				echo '<span class="value">';
					echo '<span class="uni-value">' . $player_university->partInformaticEngineering . '</span> %';
				echo '</span>';
				echo '<span class="progress-bar">';
					echo '<span style="width:' . $player_university->partInformaticEngineering . '%;" class="content"></span>';
				echo '</span>';
				echo '<span class="group-link">';
					echo '<a href="#" class="uni-invest-button decrease" data-type="informatic" />-</a>';
					echo '<a href="#" class="uni-invest-button increase" data-type="informatic" />+</a>';
				echo '</span>';
			echo '</div>';
			echo '<div class="number-box" id="uni-percent-life" data-percent="' . $player_university->partLifeSciences . '">';
				echo '<span class="label">sciences politiques</span>';
				echo '<span class="value">';
					echo '<span class="uni-value">' . $player_university->partLifeSciences . '</span> %';
				echo '</span>';
				echo '<span class="progress-bar">';
					echo '<span style="width:' . $player_university->partLifeSciences . '%;" class="content"></span>';
				echo '</span>';
				echo '<span class="group-link">';
					echo '<a href="#" class="uni-invest-button decrease" data-type="life" />-</a>';
					echo '<a href="#" class="uni-invest-button increase" data-type="life" />+</a>';
				echo '</span>';
			echo '</div>';
			echo '<div class="number-box" id="uni-percent-social" data-percent="' . $player_university->partSocialPoliticalSciences . '">';
				echo '<span class="label">sciences économiques & sociales</span>';
				echo '<span class="value">';
					echo '<span class="uni-value">' . $player_university->partSocialPoliticalSciences . '</span> %';
				echo '</span>';
				echo '<span class="progress-bar">';
					echo '<span style="width:' . $player_university->partSocialPoliticalSciences . '%;" class="content"></span>';
				echo '</span>';
				echo '<span class="group-link">';
					echo '<a href="#" class="uni-invest-button decrease" data-type="social" />-</a>';
					echo '<a href="#" class="uni-invest-button increase" data-type="social" />+</a>';
				echo '</span>';
			echo '</div>';

			$rest = 100 - $player_university->partLifeSciences - $player_university->partNaturalSciences
					- $player_university->partInformaticEngineering - $player_university->partSocialPoliticalSciences;
			$visibility = ($rest == 0) ? 'style="display: none;"' : '';
			echo '<div class="number-box" id="uni-percent-rest" data-percent="' . $rest . '" ' . $visibility . '>';
				echo '<span class="label">non attribué</span>';
				echo '<span class="value">';
					echo '<span class="uni-value">' . $rest . '</span> %';
				echo '</span>';
			echo '</div>';

			echo '<p>Augmente ou baisse tes investissements par 10% avec "CTRL + clic" ou "CMD + clic".</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component uni">';
	echo '<div class="head skin-2">';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="number-box" id="uni-invest-natural" data-invest="' . $player_university->partNaturalSciences . '">';
				echo '<span class="label">part pour les sciences naturelles</span>';
				echo '<span class="value">';
					echo '<span class="uni-value">' . Format::numberFormat(round($player_university->iUniversity * $player_university->partNaturalSciences / 100)) . '</span>';
					echo ' <img alt="crédit" src="' . MEDIA . 'resources/credit.png" class="icon-color">';
				echo '</span>';
			echo '</div>';

			echo '<div class="build-item">';
				echo '<div class="name">';
					echo '<img src="' . MEDIA . 'university/mathematics.png" alt="" />';
					echo '<strong>' . $researchHelper->getInfo(Research::MATH, 'name') . '</strong>';
					echo '<em>niveau ' . $research_university->mathLevel . '</em>';
				echo '</div>';
			echo '</div>';
			echo '<div class="build-item">';
				echo '<div class="name">';
					echo '<img src="' . MEDIA . 'university/chemistry.png" alt="" />';
					echo '<strong>' . $researchHelper->getInfo(Research::CHEM, 'name') . '</strong>';
					echo '<em>niveau ' . $research_university->chemLevel . '</em>';
				echo '</div>';
			echo '</div>';
			echo '<div class="build-item">';
				echo '<div class="name">';
					echo '<img src="' . MEDIA . 'university/physics.png" alt="" />';
					echo '<strong>' . $researchHelper->getInfo(Research::PHYS, 'name') . '</strong>';
					echo '<em>niveau ' . $research_university->physLevel . '</em>';
				echo '</div>';
			echo '</div>';

			echo '<hr />';

			echo '<div class="number-box" id="uni-invest-life" data-invest="' . $player_university->partLifeSciences . '">';
				echo '<span class="label">part pour les sciences politiques</span>';
				echo '<span class="value">';
					echo '<span class="uni-value">' . Format::numberFormat(round($player_university->iUniversity * $player_university->partLifeSciences / 100)) . '</span>';
					echo ' <img alt="crédit" src="' . MEDIA . 'resources/credit.png" class="icon-color">';
				echo '</span>';
			echo '</div>';

			echo '<div class="build-item">';
				echo '<div class="name">';
					echo '<img src="' . MEDIA . 'university/law.png" alt="" />';
					echo '<strong>' . $researchHelper->getInfo(Research::LAW, 'name') . '</strong>';
					echo '<em>niveau ' . $research_university->bioLevel . '</em>';
				echo '</div>';
			echo '</div>';
			echo '<div class="build-item">';
				echo '<div class="name">';
					echo '<img src="' . MEDIA . 'university/communication.png" alt="" />';
					echo '<strong>' . $researchHelper->getInfo(Research::COMM, 'name') . '</strong>';
					echo '<em>niveau ' . $research_university->mediLevel . '</em>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component uni">';
	echo '<div class="head skin-2">';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="number-box" id="uni-invest-informatic" data-invest="' . $player_university->partInformaticEngineering . '">';
				echo '<span class="label">part pour l\'ingénierie informatique</span>';
				echo '<span class="value">';
					echo '<span class="uni-value">' . Format::numberFormat(round($player_university->iUniversity * $player_university->partInformaticEngineering / 100)) . '</span>';
					echo ' <img alt="crédit" src="' . MEDIA . 'resources/credit.png" class="icon-color">';
				echo '</span>';
			echo '</div>';

			echo '<div class="build-item">';
				echo '<div class="name">';
					echo '<img src="' . MEDIA . 'university/networks.png" alt="" />';
					echo '<strong>' . $researchHelper->getInfo(Research::NETWORK, 'name') . '</strong>';
					echo '<em>niveau ' . $research_university->networkLevel . '</em>';
				echo '</div>';
			echo '</div>';
			echo '<div class="build-item">';
				echo '<div class="name">';
					echo '<img src="' . MEDIA . 'university/algorithmic.png" alt="" />';
					echo '<strong>' . $researchHelper->getInfo(Research::ALGO, 'name') . '</strong>';
					echo '<em>niveau ' . $research_university->algoLevel . '</em>';
				echo '</div>';
			echo '</div>';
			echo '<div class="build-item">';
				echo '<div class="name">';
					echo '<img src="' . MEDIA . 'university/statistics.png" alt="" />';
					echo '<strong>' . $researchHelper->getInfo(Research::STAT, 'name') . '</strong>';
					echo '<em>niveau ' . $research_university->statLevel . '</em>';
				echo '</div>';
			echo '</div>';

			echo '<hr />';

			echo '<div class="number-box" id="uni-invest-social" data-invest="' . $player_university->partSocialPoliticalSciences . '">';
				echo '<span class="label">part pour les sciences économiques & sociales</span>';
				echo '<span class="value">';
					echo '<span class="uni-value">' . Format::numberFormat(round($player_university->iUniversity * $player_university->partSocialPoliticalSciences / 100)) . '</span>';
					echo ' <img alt="crédit" src="' . MEDIA . 'resources/credit.png" class="icon-color">';
				echo '</span>';
			echo '</div>';

			echo '<div class="build-item">';
				echo '<div class="name">';
					echo '<img src="' . MEDIA . 'university/economy.png" alt="" />';
					echo '<strong>Economie</strong>';
					echo '<em>niveau ' . $research_university->econoLevel . '</em>';
				echo '</div>';
			echo '</div>';
			echo '<div class="build-item">';
				echo '<div class="name">';
					echo '<img src="' . MEDIA . 'university/psychology.png" alt="" />';
					echo '<strong>Psychologie</strong>';
					echo '<em>niveau ' . $research_university->psychoLevel . '</em>';
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
			echo '<p class="long-info">L’Université est une résidence qui assemble en son sein la recherche, la conservation et la transmission 
			de différents domaines de la connaissance. Ce bâtiment vous permettra d’attribuer des crédits dans différents domaines de recherche. 
			Ces différents domaines de recherche évolueront en fonction du nombre de crédits investis dans ceux-ci.<br /><br />
			Cette résidence est la plus importante de votre planète, car en investissant des crédits elle permettra de débloquer, de donner des 
			bonus non seulement aux bâtiments de votre planète, mais également à ceux de votre base orbitale et à chacun de vos vaisseaux.<br /><br />
			Chaque fois qu’un type de technologie est découvert dans votre université, une nouvelle technologie sera alors disponible dans la Technosphère.<br /><br />
			Pensez à toujours allouer des crédits dans votre université pour optimiser au maximum vos flottes et vos infrastructures.</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';
