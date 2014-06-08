<?php
echo '<h1>Suppression de la colonne "description" dans la table "player"</h1>';

$db = DataBaseAdmin::getInstance();
$qr = $db->prepare("
  ALTER TABLE player
  DROP COLUMN description
");
$qr->execute();

?>