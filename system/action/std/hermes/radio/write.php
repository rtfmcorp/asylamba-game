<?php
include_once GAIA;
# write a message in a radio action

# int sector 		id du secteur de la radio dans laquelle on veut poster
# string content 	contenu du message

for ($i = 0; $i < CTR::$data->get('playerBase')->get('ob')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ob')->get($i)->get('sector');
}
for ($i = 0; $i < CTR::$data->get('playerBase')->get('ms')->size(); $i++) { 
	$verif[] = CTR::$data->get('playerBase')->get('ms')->get($i)->get('sector');
}
$verif = array_unique($verif);

if (CTR::$get->exist('sector')) {
	$sector = CTR::$get->get('sector');
} elseif (CTR::$post->exist('sector')) {
	$sector = CTR::$post->get('sector');
} else {
	$sector = FALSE;
}
if (CTR::$get->exist('content')) {
	$content = CTR::$get->get('content');
} elseif (CTR::$post->exist('content')) {
	$content = CTR::$post->get('content');
} else {
	$content = FALSE;
}

// protection des inputs
$p = new Parser();
$content = $p->protect($content);

if ($sector !== FALSE AND $content !== FALSE AND in_array($sector, $verif) AND $content !== '') { 
	$sector = intval($sector);

	$message = new MessageRadio();
	$message->rPlayer = CTR::$data->get('playerId');
	$message->rSystem = $sector;
	$message->edit($content);
	ASM::$mrm->add($message);

	CTR::$alert->add('Message envoyé', ALERT_STD_SUCCESS);
} else {
	CTR::$alert->add('pas assez d\'informations pour écrire un message dans une radio', ALERT_STD_FILLFORM);
}
?>