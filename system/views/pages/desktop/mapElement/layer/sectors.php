<?php
echo '<div id="sectors">';
	echo '<svg viewBox="0, 0, 5000, 5000" xmlns="http://www.w3.org/2000/svg">';
		for ($i = 0; $i < $sm->size(); $i++) {
			echo '<polygon ';
				echo 'class="ally' . $sm->get($i)->getRColor() . '" ';
				echo 'points="' . $gc->getCoordPolygon($i, 20, 0) . '" ';
				echo 'data-x-brc="' . $sm->get($i)->getXBarycentric() . '" ';
				echo 'data-y-brc="' . $sm->get($i)->getYBarycentric() . '" ';
			echo '/>';
		}
	echo '</svg>';
echo '</div>';
?>