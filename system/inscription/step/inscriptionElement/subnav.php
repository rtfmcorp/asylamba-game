<?php

use Asylamba\Classes\Worker\CTR;

echo '<div id="inscription-subnav">';
	echo '<div class="bind"></div>';
	echo '<div class="head">';
		echo '<h2>';
			if (CTR::$data->get('inscription')->exist('pseudo')) {
				echo '<h2>' . CTR::$data->get('inscription')->get('pseudo') . '</h2>';
			}
		echo '</h2>';
		if (CTR::$data->get('inscription')->exist('avatar')) {
			echo '<img src="' . MEDIA . 'avatar/big/' . CTR::$data->get('inscription')->get('avatar') . '.png" alt="" />';
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