<?php
include_once APOLLON;
include_once ZEUS;
# write a bug report action

# int type 			type du bug
# string message 	message
# string url 		url de la page

$type = Utils::getHTTPData('type');
$message = Utils::getHTTPData('message');
$url = Utils::getHTTPData('url');


// protection des inputs
$p = new Parser();
$message = $p->protect($message);

if ($type !== FALSE AND $message !== FALSE AND $url !== FALSE AND $message !== '') {
	$S_PAM1 = ASM::$pam->getCurrentSession();
	ASM::$pam->newSession(ASM_UMODE);
	ASM::$pam->load(array('id' => CTR::$data->get('playerId')));

	$bug = new BugTracker();
	$bug->url = $url;
	$bug->rPlayer = CTR::$data->get('playerId');
	$bug->bindKey = ASM::$pam->get(0)->getBind();
	$bug->type = $type;
	$bug->dSending = Utils::now();
	$bug->message = $message;
	$bug->statement = BugTracker::ST_WAITING;

	$S_BTM1 = ASM::$btm->getCurrentSession();
	ASM::$btm->newSession(ASM_UMODE);
	ASM::$btm->add($bug);

	ASM::$btm->changeSession($S_BTM1);
	ASM::$pam->changeSession($S_PAM1);

	CTR::$alert->add('Rapport d\'erreur envoyé. Merci pour votre aide.', ALERT_STD_SUCCESS);
} else {
	CTR::$alert->add('pas assez d\'informations pour envoyer un rapport d\'erreur', ALERT_STD_FILLFORM);
}
?>