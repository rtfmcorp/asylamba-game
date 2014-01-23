<?php
echo '<h1>Ajout de la table transaction</h1>';

$db = DataBaseAdmin::getInstance();
$qr = $db->prepare("CREATE TABLE IF NOT EXISTS `transaction` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rPlayer` int(11) NOT NULL,
  `rPlace` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL COMMENT '0 = resource, 1 = ship, 2 = commander',
  `quantity` int(11) NOT NULL,
  `identifier` int(11) DEFAULT NULL,
  `price` int(11) NOT NULL,
  `commercialShipQuantity` int(11) NOT NULL,
  `statement` tinyint(4) NOT NULL COMMENT '0 = proposed, 1 = completed, 2 = canceled',
  `dPublication` datetime NOT NULL,
  `dValidation` datetime DEFAULT NULL,
  `currentRate` float DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
$qr->execute();

include_once ATHENA;
$initResources = new Transaction();
$initResources->rPlayer = 0;
$initResources->rPlacer = 0;
$initResources->type = Transaction::TYP_RESOURCE;
$initResources->quantity = 8;
$initResources->price = 10;
$initResources->commercialShipQuantity = 0;
$initResources->statement = Transaction::ST_COMPLETED;
$initResources->dPublication = Utils::now();
$initResources->dValidation = Utils::now();
$initResources->currentRate = 0.8;
ASM::$trm->add($initResources);
?>