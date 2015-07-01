<?php
$email = Utils::getHTTPData('email');

$sponsorLink = 'http://asylamba.com/invitationde-' . CTR::$data->get('playerId') . '/sur-' . APP_ID;

# sending email API call
$api = new API(GETOUT_ROOT, APP_ID, KEY_API);
$ok = $api->sendMail2($email, APP_ID, API::TEMPLATE_SPONSORSHIP, $sponsorLink);

if ($ok) {
	CTR::$alert->add('Un e-mail va être envoyé dans quelques minutes à ' . $email, ALERT_STD_SUCCESS);
} else {
	CTR::$alert->add('L\'e-mail n\'a pas pu être envoyé, veuillez ré-essayer. Si cela persiste, contactez un administrateur.', ALERT_STD_ERROR);
}

?>