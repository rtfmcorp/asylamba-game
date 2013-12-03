<?php
# generator component
# in athena.bases package

# affichage de l'université

# require
	# {orbitalBase}		ob_university

# work
include_once PROMETHEE;

ASM::$rsm->load(array('rPlayer' => CTR::$data->get('playerId')));

echo '<div class="component">';
	echo '<div class="head skin-1">';
		echo '<img src="' . MEDIA . 'orbitalbase/university.png" alt="" />';
		echo '<h2>Université</h2>';
		echo '<em>recherche & développement</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="number-box" id="uni-total-invest" data-invest="' . $ob_university->getIUniversity() . '" data-baseid="' . $ob_university->getId() . '">';
				echo '<span class="label">investissements alloués à l\'université</span>';
				echo '<span class="value">';
					echo Format::numberFormat($ob_university->getIUniversity());
					echo ' <img alt="crédit" src="' . MEDIA . 'resources/credit.png" class="icon-color">';
				echo '</span>';
			echo '</div>';

			if (CTR::$data->get('playerBonus')->get(PlayerBonus::UNI_INVEST) == 0) {
				echo '<div class="number-box grey">';
			} else {
				echo '<div class="number-box">';
			}
				echo '<span class="label">bonus d\'efficacité d\'investissement</span>';
				echo '<span class="value">';
					echo CTR::$data->get('playerBonus')->get(PlayerBonus::UNI_INVEST) . ' %';
				echo '</span>';
			echo '</div>';

			echo '<hr />';

			echo '<div class="number-box" id="uni-percent-natural" data-percent="' . $ob_university->getPartNaturalSciences() . '">';
				echo '<span class="label">sciences naturelles</span>';
				echo '<span class="value">';
					echo '<span class="uni-value">' . $ob_university->getPartNaturalSciences() . '</span> %';
					echo '<span class="progress-bar">';
						echo '<span style="width:' . $ob_university->getPartNaturalSciences() . '%;" class="content"></span>';
					echo '</span>';
				echo '</span>';
				echo '<span class="group-link">';
					echo '<a href="#" class="uni-invest-button decrease" data-type="natural" />-</a>';
					echo '<a href="#" class="uni-invest-button increase" data-type="natural" />+</a>';
				echo '</span>';
			echo '</div>';
			echo '<div class="number-box" id="uni-percent-informatic" data-percent="' . $ob_university->getPartInformaticEngineering() . '">';
				echo '<span class="label">ingénierie informatique</span>';
				echo '<span class="value">';
					echo '<span class="uni-value">' . $ob_university->getPartInformaticEngineering() . '</span> %';
				echo '</span>';
				echo '<span class="progress-bar">';
					echo '<span style="width:' . $ob_university->getPartInformaticEngineering() . '%;" class="content"></span>';
				echo '</span>';
				echo '<span class="group-link">';
					echo '<a href="#" class="uni-invest-button decrease" data-type="informatic" />-</a>';
					echo '<a href="#" class="uni-invest-button increase" data-type="informatic" />+</a>';
				echo '</span>';
			echo '</div>';
			echo '<div class="number-box" id="uni-percent-life" data-percent="' . $ob_university->getPartLifeSciences() . '">';
				echo '<span class="label">sciences de la vie</span>';
				echo '<span class="value">';
					echo '<span class="uni-value">' . $ob_university->getPartLifeSciences() . '</span> %';
				echo '</span>';
				echo '<span class="progress-bar">';
					echo '<span style="width:' . $ob_university->getPartLifeSciences() . '%;" class="content"></span>';
				echo '</span>';
				echo '<span class="group-link">';
					echo '<a href="#" class="uni-invest-button decrease" data-type="life" />-</a>';
					echo '<a href="#" class="uni-invest-button increase" data-type="life" />+</a>';
				echo '</span>';
			echo '</div>';
			echo '<div class="number-box" id="uni-percent-social" data-percent="' . $ob_university->getPartSocialPoliticalSciences() . '">';
				echo '<span class="label">sciences sociales & politiques</span>';
				echo '<span class="value">';
					echo '<span class="uni-value">' . $ob_university->getPartSocialPoliticalSciences() . '</span> %';
				echo '</span>';
				echo '<span class="progress-bar">';
					echo '<span style="width:' . $ob_university->getPartSocialPoliticalSciences() . '%;" class="content"></span>';
				echo '</span>';
				echo '<span class="group-link">';
					echo '<a href="#" class="uni-invest-button decrease" data-type="social" />-</a>';
					echo '<a href="#" class="uni-invest-button increase" data-type="social" />+</a>';
				echo '</span>';
			echo '</div>';

			$rest = 100 - $ob_university->getPartLifeSciences() - $ob_university->getPartNaturalSciences()
					- $ob_university->getPartInformaticEngineering() - $ob_university->getPartSocialPoliticalSciences();
			$visibility = ($rest == 0) ? 'style="display: none;"' : '';
			echo '<div class="number-box" id="uni-percent-rest" data-percent="' . $rest . '" ' . $visibility . '>';
				echo '<span class="label">non attribué</span>';
				echo '<span class="value">';
					echo '<span class="uni-value">' . $rest . '</span> %';
				echo '</span>';
			echo '</div>';
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component uni">';
	echo '<div class="head skin-2">';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="number-box" id="uni-invest-natural" data-invest="' . $ob_university->getPartNaturalSciences() . '">';
				echo '<span class="label">part pour les sciences naturelles</span>';
				echo '<span class="value">';
					echo '<span class="uni-value">' . Format::numberFormat(round($ob_university->getIUniversity() * $ob_university->getPartNaturalSciences() / 100)) . '</span>';
					echo ' <img alt="crédit" src="' . MEDIA . 'resources/credit.png" class="icon-color">';
				echo '</span>';
			echo '</div>';

			echo '<div class="build-item">';
				echo '<div class="name">';
					echo '<img src="' . MEDIA . 'university/mathematics.png" alt="" />';
					echo '<strong>Mathématiques</strong>';
					echo '<em>niveau ' . ASM::$rsm->get(0)->mathLevel . '</em>';
				echo '</div>';
			echo '</div>';
			echo '<div class="build-item">';
				echo '<div class="name">';
					echo '<img src="' . MEDIA . 'university/chemistry.png" alt="" />';
					echo '<strong>Chimie</strong>';
					echo '<em>niveau ' . ASM::$rsm->get(0)->chemLevel . '</em>';
				echo '</div>';
			echo '</div>';
			echo '<div class="build-item">';
				echo '<div class="name">';
					echo '<img src="' . MEDIA . 'university/physics.png" alt="" />';
					echo '<strong>Physique</strong>';
					echo '<em>niveau ' . ASM::$rsm->get(0)->physLevel . '</em>';
				echo '</div>';
			echo '</div>';

			echo '<hr />';

			echo '<div class="number-box" id="uni-invest-life" data-invest="' . $ob_university->getPartLifeSciences() . '">';
				echo '<span class="label">part pour les sciences de la vie</span>';
				echo '<span class="value">';
					echo '<span class="uni-value">' . Format::numberFormat(round($ob_university->getIUniversity() * $ob_university->getPartLifeSciences() / 100)) . '</span>';
					echo ' <img alt="crédit" src="' . MEDIA . 'resources/credit.png" class="icon-color">';
				echo '</span>';
			echo '</div>';

			echo '<div class="build-item">';
				echo '<div class="name">';
					echo '<img src="' . MEDIA . 'university/biology.png" alt="" />';
					echo '<strong>Biologie</strong>';
					echo '<em>niveau ' . ASM::$rsm->get(0)->bioLevel . '</em>';
				echo '</div>';
			echo '</div>';
			echo '<div class="build-item">';
				echo '<div class="name">';
					echo '<img src="' . MEDIA . 'university/medicine.png" alt="" />';
					echo '<strong>Médecine</strong>';
					echo '<em>niveau ' . ASM::$rsm->get(0)->mediLevel . '</em>';
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
			echo '<div class="number-box" id="uni-invest-informatic" data-invest="' . $ob_university->getPartInformaticEngineering() . '">';
				echo '<span class="label">part pour l\'ingénierie informatique</span>';
				echo '<span class="value">';
					echo '<span class="uni-value">' . Format::numberFormat(round($ob_university->getIUniversity() * $ob_university->getPartInformaticEngineering() / 100)) . '</span>';
					echo ' <img alt="crédit" src="' . MEDIA . 'resources/credit.png" class="icon-color">';
				echo '</span>';
			echo '</div>';

			echo '<div class="build-item">';
				echo '<div class="name">';
					echo '<img src="' . MEDIA . 'university/networks.png" alt="" />';
					echo '<strong>Réseaux</strong>';
					echo '<em>niveau ' . ASM::$rsm->get(0)->networkLevel . '</em>';
				echo '</div>';
			echo '</div>';
			echo '<div class="build-item">';
				echo '<div class="name">';
					echo '<img src="' . MEDIA . 'university/algorithmic.png" alt="" />';
					echo '<strong>Algorithmique</strong>';
					echo '<em>niveau ' . ASM::$rsm->get(0)->algoLevel . '</em>';
				echo '</div>';
			echo '</div>';
			echo '<div class="build-item">';
				echo '<div class="name">';
					echo '<img src="' . MEDIA . 'university/statistics.png" alt="" />';
					echo '<strong>Statistiques</strong>';
					echo '<em>niveau ' . ASM::$rsm->get(0)->statLevel . '</em>';
				echo '</div>';
			echo '</div>';

			echo '<hr />';

			echo '<div class="number-box" id="uni-invest-social" data-invest="' . $ob_university->getPartSocialPoliticalSciences() . '">';
				echo '<span class="label">part pour les sciences sociales & politiques</span>';
				echo '<span class="value">';
					echo '<span class="uni-value">' . Format::numberFormat(round($ob_university->getIUniversity() * $ob_university->getPartSocialPoliticalSciences() / 100)) . '</span>';
					echo ' <img alt="crédit" src="' . MEDIA . 'resources/credit.png" class="icon-color">';
				echo '</span>';
			echo '</div>';

			echo '<div class="build-item">';
				echo '<div class="name">';
					echo '<img src="' . MEDIA . 'university/economy.png" alt="" />';
					echo '<strong>Economie</strong>';
					echo '<em>niveau ' . ASM::$rsm->get(0)->econoLevel . '</em>';
				echo '</div>';
			echo '</div>';
			echo '<div class="build-item">';
				echo '<div class="name">';
					echo '<img src="' . MEDIA . 'university/psychology.png" alt="" />';
					echo '<strong>Psychologie</strong>';
					echo '<em>niveau ' . ASM::$rsm->get(0)->psychoLevel . '</em>';
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
?>