<?php

use Asylamba\Classes\Exception\ErrorException;
use Asylamba\Modules\Hephaistos\Model\Feedback;

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('session_wrapper');

if (empty($feedbackId = $request->query->get('id'))) {
    throw new ErrorException('Aucun identifiant renseigné');
}
if (empty($type = $request->query->get('type'))) {
    throw new ErrorException('Le type de feedback doit être renseigné');
}

$manager = $this->getContainer()->get(
    ($type === Feedback::TYPE_BUG)
    ? 'hephaistos.bug_manager'
    : 'hephaistos.evolution_manager'
);
$feedback = $manager->get($feedbackId);

# background paralax
echo '<div id="background-paralax" class="message"></div>';

# inclusion des elements
include 'projectElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
?>
<div id="content">
    <?php include COMPONENT . 'publicity.php'; ?>
    <?php include COMPONENT . '/project/feedback.php'; ?>
</div>