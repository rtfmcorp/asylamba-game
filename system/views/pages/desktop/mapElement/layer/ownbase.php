<?php
echo '<div id="own-base">';
	echo '<svg viewBox="0, 0, 5000, 5000" xmlns="http://www.w3.org/2000/svg">';
		for ($i = 0; $i < ASM::$obm->size(); $i++) {
			$base = ASM::$obm->get($i);
			echo '<circle cx="' . ($base->getXSystem() * 20) . '" cy="' . ($base->getYSystem() * 20) . '" r="22" />';
			if ($base->getId() == $defaultBase->getId()) {
				echo '<circle cx="' . ($base->getXSystem() * 20) . '" cy="' . ($base->getYSystem() * 20) . '" r="16">';
					echo '<animate attributeType="XML" from="16" to="60" begin="0s" dur="1200ms" repeatCount="indefinite" attributeName="r" />';
					echo '<animate attributeType="CSS" from="0.2" to="0" begin="0s" dur="1200ms" repeatCount="indefinite" attributeName="opacity" />';
				echo '</circle>';
			
			} else {
				echo '<circle cx="' . ($base->getXSystem() * 20) . '" cy="' . ($base->getYSystem() * 20) . '" r="28" />';
			}
		}
	echo '</svg>';
echo '</div>';
?>