<?php
# recycling component
# in athena.bases package

# affichage du Centre de Recyclage

# require
	# {orbitalBase}		ob_recycling

echo '<div class="component building">';
	echo '<div class="head skin-1">';
		echo '<img src="' . MEDIA . 'orbitalbase/recycling.png" alt="" />';
		echo '<h2>' . OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::RECYCLING, 'frenchName') . '</h2>';
		echo '<em>niveau ' . $ob_recycling->getLevelRecycling() . '</em>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h3>Efficacité des recycleurs par niveau</h3>';
			echo '<ul class="list-type-1">';
				$level = $ob_recycling->getLevelRecycling();
				$from  = ($level < 3)  ? 1  : $level - 2;
				$to    = ($level > 25) ? 31 : $level + 5;
				for ($i = $from; $i < $to; $i++) {
					echo ($i == $level) ? '<li class="strong">' : '<li>';
						echo '<span class="label">niveau ' . $i . '</span>';
						echo '<span class="value">';
							$efficiency = OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::RECYCLING, 'level', $i, 'recyclingEfficiency');
							echo Format::numberFormat($efficiency) . ' %';
						echo '</span>';
					echo '</li>';
				}
				echo '</ul>';
		echo '</div>';
	echo '</div>';
echo '</div>';

echo '<div class="component">';
	echo '<div class="head skin-2">';
		echo '<h2>À propos</h2>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<p class="long-info">' . OrbitalBaseResource::getBuildingInfo(OrbitalBaseResource::RECYCLING, 'description') . '</p>';
		echo '</div>';
	echo '</div>';
echo '</div>';
?>