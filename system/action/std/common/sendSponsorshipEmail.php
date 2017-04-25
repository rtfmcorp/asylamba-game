<?php

use Asylamba\Classes\Worker\API;
use Asylamba\Classes\Library\Flashbag;
use Asylamba\Classes\Exception\ErrorException;

$session = $this->getContainer()->get('app.session');
$request = $this->getContainer()->get('app.request');

$email = $request->request->get('email');

$sponsorLink = GETOUT_ROOT . 'action/a-invitation/i-' . $session->get('playerId') . '/s-' . APP_ID;

# sending email API call
$ok = $this->getContainer()->get('api')->sendMail2($email, APP_ID, API::TEMPLATE_SPONSORSHIP, $session->get('playerId'));

if ($ok) {
	$session->addFlashbag('Un e-mail va être envoyé dans quelques minutes à ' . $email, Flashbag::TYPE_SUCCESS);
} else {
	throw new ErrorException('L\'e-mail n\'a pas pu être envoyé, veuillez ré-essayer. Si cela persiste, contactez un administrateur.');
}

