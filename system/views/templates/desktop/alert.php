<?php
echo '<ul id="alert"></ul>';

# affichage
if (CTR::$alert->size() > 0) {
	echo '<ul id="alert-content">';
		for ($i = 0; $i < CTR::$alert->size(); $i++) {
			$alert = CTR::$alert->get($i);
			if (in_array($alert[1], array(ALERT_BUG_INFO, ALERT_BUG_ERROR, ALERT_BUG_SUCCESS))) {
				if (DEVMODE) {
					echo '<li data-type="' . $alert[1] . '">';
						echo $alert[0];
					echo '</li>';
				}
			} else {
				echo '<li data-type="' . $alert[1] . '">';
					echo $alert[0];
				echo '</li>';	
			}
		}
	echo '</ul>';
}

CTR::$alert->clear();
?>