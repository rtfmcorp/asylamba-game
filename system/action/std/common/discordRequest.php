<?php
$discordId = Utils::getHTTPData('discord-id');

if ($discordId !== FALSE AND $discordId !== '') {
 	
 	$chickenBot = 'http://chickenbot@cloudapp.net/register';

	$data = array('discordId' => $discordId, 'server' => 's9', 'username' => 'bertrand', 'factionColor' => 3); 
	$data_string = json_encode($data);
	$ch = curl_init($chickenBot);
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt(
		$ch, 
		CURLOPT_HTTPHEADER, 
		array('Content-Type: application/json', 'Content-Length: ' . strlen($data_string))
	);
	$answer = curl_exec($ch);
	curl_close($ch);

	if ($answer !== FALSE) {
		$data = unserialize($answer);
		# TODO : voir ce qu'il y a dans $data
		CTR::$alert->add('Ca devrait avoir marché, rendez-vous sur Discord.', ALERT_STD_SUCCESS);
	} else {
		CTR::$alert->add('Le Chicken Bot ne répond pas. Ré-essayez plus tard et/ou contactez un admin.', ALERT_STD_ERROR);
	}

} else {
	CTR::$alert->add('il faut entrer votre identifiant Discord dans le champ de texte', ALERT_STD_FILLFORM);
}
?>
