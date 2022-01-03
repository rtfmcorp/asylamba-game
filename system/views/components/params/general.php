<?php
use Asylamba\Classes\Library\Format;

$sessionToken = $this->getContainer()->get(\Asylamba\Classes\Library\Session\SessionWrapper::class)->get('token');
# void
echo '<div class="component">';
	echo '<div class="head">';
		echo '<h1>Paramètres</h1>';
	echo '</div>';
	echo '<div class="fix-body">';
		echo '<div class="body">';
			echo '<h4>Abandonner la partie</h4>';
			echo '<p>Pour abandonner la partie, cliquez sur le bouton ci-dessous. Attention, cette action est irréversible.</p>';
			echo '<p>Si vous souhaitez recommencer, vous pouvez abandonner la partie ici et recommencer dans cette partie depuis le portail principal.</p>';
			echo '<a class="more-button confirm" href="' . Format::actionBuilder('abandonserver', $sessionToken) . '">Abandonner la partie</a>';
		echo '</div>';
	echo '</div>';
echo '</div>';
