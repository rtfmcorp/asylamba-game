<?php
echo '<h1>Ajout de la table color</h1>';

$db = DataBaseAdmin::getInstance();
$qr = $db->prepare("CREATE TABLE IF NOT EXISTS `color` (
    `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
    `alive` tinyint(1) NOT NULL DEFAULT '1',
    `credits` int(11) NOT NULL,
    `players` int(11) NOT NULL,
    `activePlayers` int(11) NOT NULL,
    `points` int(11) NOT NULL,
    `sectors` smallint(6) NOT NULL,
    `electionStatement` smallint(6) NOT NULL,
    `dLastElection` datetime NOT NULL,
    PRIMARY KEY (`id`)
  ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;");
$qr->execute();

# add content
$qr = $db->prepare("INSERT INTO `color` (`id`, `alive`, `credits`, `players`, `activePlayers`, `points`, `sectors`, `electionStatement`, `dLastElection`) VALUES
(1, 1, 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00'),
(2, 1, 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00'),
(3, 1, 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00'),
(4, 1, 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00'),
(5, 1, 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00'),
(6, 1, 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00'),
(7, 1, 0, 0, 0, 0, 0, 0, '0000-00-00 00:00:00');");
$qr->execute();

?>