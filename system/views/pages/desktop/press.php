<?php

$request = $this->getContainer()->get('app.request');
$session = $this->getContainer()->get('session_wrapper');
$newsManager = $this->getContainer()->get('hermes.news_manager');

# background paralax
echo '<div id="background-paralax" class="message"></div>';

$mode = $request->query->get('mode', 'gazette');

# inclusion des elements
include 'pressElement/subnav.php';
include 'defaultElement/movers.php';

# contenu spécifique
?>
<div id="content">
    <?php include COMPONENT . 'publicity.php'; ?>
    <?php include COMPONENT . "/press/$mode.php"; ?>
</div>