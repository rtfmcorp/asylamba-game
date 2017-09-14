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
    <div class="component invisible">
        
    </div>
    <?php include COMPONENT . '/press/' . (($mode === 'gazette') ? 'gazette' : 'newspaper') . '.php'; ?>
</div>