<?php
# write a message in a radio action

# int sector 		id du secteur de la radio dans laquelle on veut poster
# string content 	contenu du message

//
//$request = $this->getContainer()->get('app.request');
//$session = $this->getContainer()->get('app.session');
//$parser = $this->getContainer()->get('parser');
//
//for ($i = 0; $i < $session->get('playerBase')->get('ob')->size(); $i++) { 
//	$verif[] = $session->get('playerBase')->get('ob')->get($i)->get('sector');
//}
//for ($i = 0; $i < $session->get('playerBase')->get('ms')->size(); $i++) { 
//	$verif[] = $session->get('playerBase')->get('ms')->get($i)->get('sector');
//}
//$verif = array_unique($verif);
//
//$sector = Utils::getHTTPData('sector');
//$content = Utils::getHTTPData('content');
//
//
//// protection des inputs
//$content = $parser->protect($content);
//
//if ($sector !== FALSE AND $content !== FALSE AND in_array($sector, $verif) AND $content !== '') { 
//	$sector = intval($sector);
//
//	$message = new MessageRadio();
//	$message->rPlayer = $session->get('playerId');
//	$message->rSystem = $sector;
//	$message->edit($content);
//	ASM::$mrm->add($message);
//
//	CTR::$alert->add('Message envoyé', ALERT_STD_SUCCESS);
//} else {
//	CTR::$alert->add('pas assez d\'informations pour écrire un message dans une radio', ALERT_STD_FILLFORM);
//}