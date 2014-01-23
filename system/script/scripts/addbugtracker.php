<?php
echo '<h1>Ajout de la table bugTracker</h1>';

$db = DataBaseAdmin::getInstance();
$qr = $db->prepare("CREATE TABLE IF NOT EXISTS `bugtracker` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(150) NOT NULL,
  `rPlayer` int(11) NOT NULL,
  `bindKey` varchar(30) NOT NULL,
  `type` smallint(6) NOT NULL,
  `dSending` datetime NOT NULL,
  `message` text NOT NULL,
  `statement` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0 = waiting, 1 = archived, 2 = deleted',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

$qr->execute();
?>