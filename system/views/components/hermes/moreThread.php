<?php
# moreThread componant
# in hermes package

# affiche rien

# require
	# *int 				moreThread_page

if (!isset($moreThread_page)) {
	$moreThread_page = 1;
}

echo '<div class="component">';
	echo '<div class="head">';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<p>';
				echo '<a href="' . APP_ROOT . 'ajax/a-morethread/page-' . $moreThread_page . '" class="alone-button more-thread">Afficher plus de conversations</a></p>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>