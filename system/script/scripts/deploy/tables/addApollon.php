<?php
echo '<h1>Module Apollon</h1>';

$db = DataBaseAdmin::getInstance();

#--------------------------------------------------------------------------------------------
echo '<h2>Ajout de la table bugTracker</h2>';

$db->query("DROP TABLE IF EXISTS `bugTracker`");
$db->query("CREATE TABLE IF NOT EXISTS `bugTracker` (
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

#--------------------------------------------------------------------------------------------

echo '<br /><hr />';
?>