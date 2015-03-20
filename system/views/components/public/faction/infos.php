<?php
echo '<div class="component player rank">';
	echo '<div class="head skin-2">';
		echo '<h2>A propos</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4 style="text-align: center;">' . ColorResource::getInfo($faction->id, 'devise') . '</h4>';

			echo '<p class="long-info">' . $faction->getParsedDescription() . '</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>