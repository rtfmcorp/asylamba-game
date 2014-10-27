<?php
echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h1>Sénat</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<div class="number-box grey">';
				echo '<span class="label">Lois en cours de votation</span>';
				echo '<span class="value">';
					echo Format::number(ASM::$lam->size());
				echo '</span>';
			echo '</div>';

			echo '<p class="info">Infos sur les lois</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';