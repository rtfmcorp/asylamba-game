<?php
echo '<h1>ajout de l\'attribut "factionPoint" dans la table player</h1>';

$db = DataBaseAdmin::getInstance();
$qr = $db->prepare("
  ALTER TABLE player
  ADD COLUMN factionPoint int(11) NOT NULL AFTER experience
");
$qr->execute();