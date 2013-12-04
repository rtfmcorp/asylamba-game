<?php
# background paralax
echo '<div id="background-paralax" class="technology"></div>';

# inclusion des elements
include 'profilElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	echo '<div class="component size1">';
	echo '<div class="head">';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<a href="#" id="button-add">add</a>';
		echo '</div>';
	echo '</div>';
echo '</div>';
echo '</div>';
?>