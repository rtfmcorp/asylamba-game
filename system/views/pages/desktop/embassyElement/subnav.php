<?php
echo '<div id="subnav">';
	echo '<button class="move-side-bar top" data-dir="up"> </button>';
	echo '<div class="overflow">';
		$active = (in_array(CTR::getPage(), array('embassy'))) ? 'active' : NULL;
		echo '<a href="' . APP_ROOT . 'embassy" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'profil/diary.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Journal';
			echo '</span>';
		echo '</a>';
	echo '</div>';
	echo '<button class="move-side-bar bottom" data-dir="down"> </button>';
echo '</div>';
?>