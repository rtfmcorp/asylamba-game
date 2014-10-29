<?php
echo '<h1>Module Demeter</h1>';

$db = DataBaseAdmin::getInstance();
#--------------------------------------------------------------------------------------------
echo '<h2>modificationde la table color</h2>';

$qr = $db->query("ALTER TABLE `color`
  ADD `isClosed` tinyint(1) NOT NULL DEFAULT '0' AFTER electionStatement;");

$qr->execute();
?>