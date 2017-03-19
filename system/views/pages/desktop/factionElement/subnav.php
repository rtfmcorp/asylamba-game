<?php

use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Modules\Zeus\Model\Player;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('app.session');

echo '<div id="subnav">';
	echo '<button class="move-side-bar top" data-dir="up"> </button>';
	echo '<div class="overflow">';
		$active = (!$request->query->has('view') OR $request->query->get('view') == 'overview') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'faction/view-overview" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'faction/nav/overview.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Vue générale';
			echo '</span>';
		echo '</a>';

		if (in_array($faction->electionStatement, array(Color::CAMPAIGN, Color::ELECTION))) {
			$active = ($request->query->get('view') == 'election') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'faction/view-election" class="item ' . $active . '">';
				echo '<span class="picto">';
					echo '<img src="' . MEDIA . 'faction/nav/election.png" alt="" />';
				echo '</span>';
				echo '<span class="content skin-1">';
					echo '<span>Election';
				echo '</span>';
			echo '</a>';
		}

		$active = ($request->query->get('view') == 'forum') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'faction/view-forum" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'faction/nav/forum.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Forum';
			echo '</span>';
		echo '</a>';

		if (in_array($session->get('playerInfo')->get('status'), array(Player::CHIEF, Player::WARLORD, Player::TREASURER, Player::MINISTER))) {
			$active = ($request->query->get('view') == 'government') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'faction/view-government" class="item ' . $active . '">';
				echo '<span class="picto">';
					echo '<img src="' . MEDIA . 'faction/nav/government.png" alt="" />';
				echo '</span>';
				echo '<span class="content skin-1">';
					echo '<span>Gouvernement';
				echo '</span>';
			echo '</a>';
		}

		if (in_array($session->get('playerInfo')->get('status'), array(Player::CHIEF, Player::WARLORD, Player::TREASURER, Player::MINISTER, Player::PARLIAMENT))) {
			$active = ($request->query->get('view') == 'senate') ? 'active' : '';
			echo '<a href="' . APP_ROOT . 'faction/view-senate" class="item ' . $active . '">';
				echo '<span class="picto">';
					echo '<img src="' . MEDIA . 'faction/law/common.png" alt="" />';
				echo '</span>';
				echo '<span class="content skin-1">';
					echo '<span>Sénat';
				echo '</span>';
			echo '</a>';
		}

		$active = ($request->query->get('view') == 'data') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'faction/view-data" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'faction/nav/data.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Registres';
			echo '</span>';
		echo '</a>';

		$active = ($request->query->get('view') == 'player') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'faction/view-player" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'faction/nav/register.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Membres';
			echo '</span>';
		echo '</a>';
	echo '</div>';
	echo '<button class="move-side-bar bottom" data-dir="down"> </button>';
echo '</div>';
