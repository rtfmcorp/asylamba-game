<?php
echo '<h1>Suppression du module de Message</h1>';

echo '<h2>Suppression de la table Message</h2>';

$db = DataBase::getInstance();
$qr = $db->prepare("DROP TABLE `message`;");
$qr->execute();