<?php
# background paralax
echo '<div id="background-paralax" class="sponsorship"></div>';

# inclusion des elements
include 'defaultElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
echo '<div id="content">';
	include COMPONENT . 'sponsorship/infos.php';
	include COMPONENT . 'sponsorship/send-mail.php';
	include COMPONENT . 'sponsorship/list-godson.php';
echo '</div>';