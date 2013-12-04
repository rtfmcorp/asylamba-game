<?php
echo '<h1>Ajout de la table commercialShipping</h1>';

$db = DataBaseAdmin::getInstance();
$qr = $db->prepare("CREATE TABLE IF NOT EXISTS `commercialShipping` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `rPlayer` int(11) NOT NULL,
  `rBase` int(11) NOT NULL,
  `rBaseDestination` int(11) NOT NULL,
  `rTransaction` int(11) DEFAULT NULL,
  `resourceTransported` int(11) DEFAULT NULL,
  `shipQuantity` int(11) NOT NULL,
  `dDeparture` datetime NOT NULL,
  `dArrival` datetime NOT NULL,
  `statement` smallint(6) NOT NULL COMMENT '0 = prêt au départ, 1 = aller, 2 = retour',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");

$qr->execute();
?>