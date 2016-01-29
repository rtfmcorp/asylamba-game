<?php
$discordId = Utils::getHTTPData('discord-id');

if ($discordId !== FALSE AND $discordId !== '') {

	$chickenBot = 'http://chickenbot.cloudapp.net:8080/register';

	$data = array("discordId" => $discordId, "server" => "s" . APP_ID, "username" => CTR::$data->get('playerInfo')->get('name'), "factionColor" => CTR::$data->get('playerInfo')->get('color')); 
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
				CTR::$alert->add('Vos droits ont été ajoutés sur Discord. Rendez-vous là-bas.', ALERT_STD_SUCCESS);
				break;
			case 'user register correctly (update Status)':
				CTR::$alert->add('Vos droits ont été mis à jour sur Discord. Rendez-vous là-bas.', ALERT_STD_SUCCESS);
				break;
			case 'Wrong factionColor':
				CTR::$alert->add('Vous êtes dans une faction inexistance, cela doit être une erreur, contactez un administrateur', ALERT_STD_ERROR);
				break;
			case 'disord user already register':
				CTR::$alert->add('Vous avez déjà vos droits sur Discord, le faire une seconde fois ne sert à rien. S\'il s\'agit d\'une erreur, contactez un administrateur.', ALERT_STD_ERROR);
				break;
			case 'userID not found in Aslymaba 2.0 server':
				CTR::$alert->add('Cet identifiant n\'existe pas. Créez un compte sur Discord et entrez votre identifiant dans le champ', ALERT_STD_ERROR);
				break;
			default:
				CTR::$alert->add('Erreur, contactez un administrateur', ALERT_STD_ERROR);
		}
		#CTR::$alert->add('message de retour : ' . $answer, ALERT_STD_SUCCESS);
	} else {
		CTR::$alert->add('Le Chicken Bot ne répond pas. Ré-essayez plus tard et/ou contactez un admin.', ALERT_STD_ERROR);
	}

} else {
	CTR::$alert->add('il faut entrer votre identifiant Discord dans le champ de texte', ALERT_STD_FILLFORM);
}
?>
