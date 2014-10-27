<?php
echo '<h1>Module Demeter</h1>';

$db = DataBaseAdmin::getInstance();
#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table forumTopic</h2>';

$db->query("ALTER TABLE `forumTopic`
  DROP statement,
  ADD `isClosed` tinyint(1) NOT NULL DEFAULT '0' AFTER rForum,
  ADD `isArchived` tinyint(1) NOT NULL DEFAULT '0' AFTER rForum,
  ADD `isUp` tinyint(1) NOT NULL DEFAULT '0' AFTER rForum;");

$qr->execute();
?>