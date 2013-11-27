<?php
include_once ZEUS;

echo '<div id="profil-subnav">';
	echo '<div class="bind"></div>';
	echo '<div class="head">';
		echo '<h2>' . CTR::$data->get('playerInfo')->get('name') . '</h2>';
		echo '<img src="' . MEDIA . 'avatar/big/' . CTR::$data->get('playerInfo')->get('avatar') . '.png" alt="' . CTR::$data->get('playerInfo')->get('name') . '" />';
		echo '<span class="level">' . CTR::$data->get('playerInfo')->get('level') . '</span>';
		echo '<span class="experience">';
			$exp = CTR::$data->get('playerInfo')->get('experience');
			$nlv = PAM_BASELVLPLAYER * (pow(2, (CTR::$data->get('playerInfo')->get('level') - 1)));
			$clv = PAM_BASELVLPLAYER * (pow(2, (CTR::$data->get('playerInfo')->get('level') - 2)));
			$prc = ((($exp - $clv) * 200) / $nlv);
			echo '<span class="value" style="height: ' . $prc . '%;"></span>';
		echo '</span>';
	echo '</div>';
	echo '<div class="body">';
		$active = (CTR::getPage() == 'profil') ? 'class="active"': '';
		echo '<a href="' . APP_ROOT . 'profil" ' . $active . '>Profil</a>';

		$active = (CTR::getPage() == 'fleet') ? 'class="active"': '';
		echo '<a href="' . APP_ROOT . 'fleet" ' . $active . '>Amirauté</a>';

		$active = (CTR::getPage() == 'financial') ? 'class="active"': '';
		echo '<a href="' . APP_ROOT . 'financial" ' . $active . '>Finance</a>';

		# $active = (CTR::getPage() == 'technology') ? 'class="active"': '';
		# echo '<a href="' . APP_ROOT . 'technology" ' . $active . '>Technologie</a>';

		# $active = (CTR::getPage() == 'spying') ? 'class="active"': '';
		# echo '<a href="' . APP_ROOT . 'spying" ' . $active . '>Renseignement</a>';

		$active = (CTR::getPage() == 'message') ? 'class="active"': '';
		echo '<a href="' . APP_ROOT . 'message" ' . $active . '>Messagerie</a>';
	echo '</div>';
	echo '<div class="foot"></div>';
echo '</div>';
?>