<?php
echo '<h1>Ajout de la colonne "stepDone" dans la table "player"</h1>';

$db = DataBaseAdmin::getInstance();
$qr = $db->prepare("
  ALTER TABLE  `player` ADD  `stepDone` BOOLEAN NOT NULL AFTER  `stepTutorial`
");
$qr->execute();

?>