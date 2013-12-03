<?php
echo '<h1>Ajout de la table transaction</h1>';

$db = DataBaseAdmin::getInstance();
$qr = $db->prepare("CREATE TABLE IF NOT EXISTS `transaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rPlayer` int(11) NOT NULL,
  `rPlace` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL COMMENT '0 = resource, 1 = ship, 2 = commander',
  `quantity` int(11) NOT NULL,
  `identifier` int(11) NOT NULL,
  `price` int(11) NOT NULL,
  `commercialShipQuantity` int(11) NOT NULL,
  `statement` tinyint(4) NOT NULL COMMENT '0 = proposed, 1 = completed',
  `dPublication` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;");

$qr->execute();
?>