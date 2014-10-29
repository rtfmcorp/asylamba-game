<?php
echo '<div id="subnav">';
	echo '<button class="move-side-bar top" data-dir="up"> </button>';
	echo '<div class="overflow">';
		$active = (!CTR::$get->exist('view') OR CTR::$get->get('view') == 'invest') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'financial/view-invest" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'financial/invest.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Investissement';
			echo '</span>';
		echo '</a>';

		$active = (CTR::$get->get('view') == 'send') ? 'active' : '';
		echo '<a href="' . APP_ROOT . 'financial/view-send" class="item ' . $active . '">';
			echo '<span class="picto">';
				echo '<img src="' . MEDIA . 'financial/send-credit.png" alt="" />';
			echo '</span>';
			echo '<span class="content skin-1">';
				echo '<span>Envoi de crédit';
			echo '</span>';
		echo '</a>';
	echo '</div>';
	echo '<button class="move-side-bar bottom" data-dir="down"> </button>';
echo '</div>';
?>