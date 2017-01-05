<?php

$session = $this->getContainer()->get('app.session');

echo '<div id="inscription-subnav">';
	echo '<div class="bind"></div>';
	echo '<div class="head">';
		echo '<h2>';
			if ($session->get('inscription')->exist('pseudo')) {
				echo '<h2>' . $session->get('inscription')->get('pseudo') . '</h2>';
			}
		echo '</h2>';
		if ($session->get('inscription')->exist('avatar')) {
			echo '<img src="' . MEDIA . 'avatar/big/' . $session->get('inscription')->get('avatar') . '.png" alt="" />';
		} else {
			echo '<img src="' . MEDIA . 'avatar/big/empty.png" alt="" />';
		}
		echo '<span class="level">1</span>';
		echo '<span class="experience">';
			echo '<span class="value" style="0%;"></span>';
		echo '</span>';
	echo '</div>';
	echo '<div class="foot"></div>';
echo '</div>';