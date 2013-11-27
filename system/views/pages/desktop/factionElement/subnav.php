<?php
include_once ZEUS;

echo '<div id="bases-subnav">';
	echo '<div class="bind"></div>';
	echo '<div class="head">';
		echo '<h2>' . ColorResource::getInfo(CTR::$data->get('playerInfo')->get('color'), 'popularName') . '</h2>';
	echo '</div>';
	echo '<div class="foot"></div>';
echo '</div>';
?>