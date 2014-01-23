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

# initialisation du taux des ressources
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
$initResources->currentRate = 1.25;
ASM::$trm->add($initResources);

# initialisation du taux des commandants
$initCommander = new Transaction();
$initCommander->rPlayer = 0;
$initCommander->rPlacer = 0;
$initCommander->type = Transaction::TYP_COMMANDER;
$initCommander->quantity = 1;
$initCommander->price = 12;
$initCommander->commercialShipQuantity = 0;
$initCommander->statement = Transaction::ST_COMPLETED;
$initCommander->dPublication = Utils::now();
$initCommander->dValidation = Utils::now();
$initCommander->currentRate = 12;
ASM::$trm->add($initCommander);

# initialisation du taux des ressources
$initCommander = new Transaction();
$initCommander->rPlayer = 0;
$initCommander->rPlacer = 0;
$initCommander->type = Transaction::TYP_SHIP;
$initCommander->quantity = 8;
$initCommander->price = 15;
$initCommander->commercialShipQuantity = 0;
$initCommander->statement = Transaction::ST_COMPLETED;
$initCommander->dPublication = Utils::now();
$initCommander->dValidation = Utils::now();
$initCommander->currentRate = 1.875;
ASM::$trm->add($initCommander);
?>