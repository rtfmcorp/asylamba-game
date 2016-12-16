<?php

use Asylamba\Classes\Library\Http\Response;

echo '<ul id="alert"></ul>';

$response = $this->getContainer()->get('app.response');

# affichage
$redir = $response->getRedirect();
$nbFlashbags = count($response->flashbag);

if ($nbFlashbags > 0 && empty($redir)) {
	echo '<ul id="alert-content">';
		for ($i = 0; $i < $nbFlashbags; ++$i) {
			$alert = $response->flashbag->get($i);
			if (in_array($alert[1], array(Response::FLASHBAG_ERROR, Response::FLASHBAG_SUCCESS))) {
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