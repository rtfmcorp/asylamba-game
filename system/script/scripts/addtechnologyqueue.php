<?php
echo '<h1>Ajout de la table technologyQueue</h1>';

$db = DataBaseAdmin::getInstance();
$qr = $db->prepare("CREATE TABLE IF NOT EXISTS `technologyQueue` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rPlayer` int(11) NOT NULL,
  `rPlace` int(11) NOT NULL,
  `technology` smallint(5) unsigned NOT NULL,
  `targetLevel` tinyint(3) unsigned NOT NULL,
  `dStart` datetime NOT NULL,
  `dEnd` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

$qr->execute();
?>