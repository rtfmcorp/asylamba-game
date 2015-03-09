<?php
echo '<h1>Update de la table message</h1>';

$db = DataBaseAdmin::getInstance();
#--------------------------------------------------------------------------------------------

$qr = $db->query("ALTER TABLE `message` CHANGE `rPlayerWriter` `rPlayerWriter` INT(10) NULL DEFAULT NULL;");
$qr = $db->query("ALTER TABLE `message` CHANGE `rPlayerReader` `rPlayerReader` INT(10) NULL DEFAULT NULL;");

?>