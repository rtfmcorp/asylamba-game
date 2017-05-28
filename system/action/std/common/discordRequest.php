<?php

use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Classes\Exception\FormException;
use Asylamba\Classes\Library\Flashbag;

$request = $this->getContainer()->get('app.request');
$session =  $this->getContainer()->get('app.session');

$discordId = $request->query->get('discord-id');

if ($discordId !== FALSE AND $discordId !== '') {

	$chickenBot = 'http://chickenbot.cloudapp.net:8080/register';

	$data = array("discordId" => $discordId, "server" => "s" . $this->getContainer()->getParameter('server_id'), "username" => $session->get('playerInfo')->get('name'), "factionColor" => $session->get('playerInfo')->get('color')); 
	$data_string = json_encode($data);
	$ch = curl_init($chickenBot);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	#curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt(
		$ch, 
		CURLOPT_HTTPHEADER, 
		array('Content-Type: application/json', 'Content-Length: ' . strlen($data_string))
	);
	$answer = curl_exec($ch);
	$json = json_decode($answer,true);
	curl_close($ch);

	if ($answer !== FALSE) {
		#$rep = unserialize($answer);
		switch($answer) {
			case 'user register correctly':
				$session->addFlashbag('Vos droits ont été ajoutés sur Discord. Rendez-vous là-bas.', Flashbag::TYPE_SUCCESS);
				break;
			case 'user register correctly (update Status)':
				$session->addFlashbag('Vos droits ont été mis à jour sur Discord. Rendez-vous là-bas.', Flashbag::TYPE_SUCCESS);
				break;
			case 'Wrong factionColor':
				throw new ErrorException('Vous êtes dans une faction inexistance, cela doit être une erreur, contactez un administrateur');
			case 'disord user already register':
				throw new ErrorException('Vous avez déjà vos droits sur Discord, le faire une seconde fois ne sert à rien. S\'il s\'agit d\'une erreur, contactez un administrateur.');
			case 'userID not found in Aslymaba 2.0 server':
				throw new ErrorException('Cet identifiant n\'existe pas. Créez un compte sur Discord et entrez votre identifiant dans le champ');
			default:
				throw new ErrorException('Erreur, contactez un administrateur');
		}
		#throw new ErrorException('message de retour : ' . $answer, ALERT_STD_SUCCESS);
	} else {
		throw new ErrorException('Le Chicken Bot ne répond pas. Ré-essayez plus tard et/ou contactez un admin.');
	}
} else {
	throw new FormException('il faut entrer votre identifiant Discord dans le champ de texte');
}
