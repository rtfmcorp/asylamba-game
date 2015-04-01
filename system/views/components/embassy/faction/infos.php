<?php
echo '<div class="component">';
	echo '<div class="head"></div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<p class="long-info">' . $faction->getParsedDescription() . '</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>