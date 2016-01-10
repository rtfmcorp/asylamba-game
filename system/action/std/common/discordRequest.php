<?php
$discordId = Utils::getHTTPData('discord-id');

if ($discordId !== FALSE AND $discordId !== '') {
 	
 	$chickenBot = 'http://chickenbot.cloudapp.net:8080';

	$data = array("discordId" => intval($discordId), "server" => "s" . APP_ID, "username" => "bertrand", "factionColor" => 3); 
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
			case 'OK':
				CTR::$alert->add('Vos droits ont été ajoutés sur Discord. Rendez-vous là-bas.', ALERT_STD_SUCCESS);
				break;
			case 'Thanks for the data':
				CTR::$alert->add('Chicken, tu dois encore gérer les retours ;-)', ALERT_STD_SUCCESS);
				break;
			default:
				CTR::$alert->add('Oxy, il faut que tu gères ce retour (auto-message)', ALERT_STD_SUCCESS);
		}
		CTR::$alert->add('message de retour : ' . $answer, ALERT_STD_SUCCESS);
	} else {
		CTR::$alert->add('Le Chicken Bot ne répond pas. Ré-essayez plus tard et/ou contactez un admin.', ALERT_STD_ERROR);
	}

} else {
	CTR::$alert->add('il faut entrer votre identifiant Discord dans le champ de texte', ALERT_STD_FILLFORM);
}
?>
