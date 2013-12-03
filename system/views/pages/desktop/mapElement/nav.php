<?php
echo '<div id="map-nav">';
	echo '<a href="#" class="sh switch-class active hb lb" data-class="active" title="afficher/cacher la petite carte" data-target="mini-map" class="active"><img src="' . MEDIA . 'map/option/minimap.png" alt="minimap" /></a>';
	echo '<a href="' . APP_ROOT . 'map/view-ranking" class="hb lb" title="classement"><img src="' . MEDIA . 'map/option/rank.png" alt="classement" /></a>';
	echo '<a href="#" class="hb lb" title="marché (pas encore implémenté)"><img src="' . MEDIA . 'map/option/market.png" alt="marché" /></a>';
	echo '<a href="#" class="hb lb" title="radio (pas encore implémenté)"><img src="' . MEDIA . 'map/option/radio.png" alt="journal & radio" /></a>';
	echo '<a href="#" class="hb lb" title="informations (pas encore implémenté)"><img src="' . MEDIA . 'map/option/info.png" alt="information" /></a>';
echo '</div>';
?>