<?php
echo '<h1>Ajout de la table commercialTax</h1>';

$db = DataBaseAdmin::getInstance();
$qr = $db->prepare("CREATE TABLE IF NOT EXISTS `commercialtax` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `exportFrom` smallint(6) NOT NULL,
  `importTo` smallint(6) NOT NULL,
  `tax` float NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;");
$qr->execute();


# add content
$qr = $db->prepare("INSERT INTO `commercialtax` (`exportFrom`, `importTo`, `tax`) VALUES
(1, 1, 5),
(1, 2, 5),
(1, 3, 5),
(1, 4, 5),
(1, 5, 5),
(1, 6, 5),
(1, 7, 5),

(2, 1, 5),
(2, 2, 5),
(2, 3, 5),
(2, 4, 5),
(2, 5, 5),
(2, 6, 5),
(2, 7, 5),

(3, 1, 5),
(3, 2, 5),
(3, 3, 5),
(3, 4, 5),
(3, 5, 5),
(3, 6, 5),
(3, 7, 5),

(4, 1, 5),
(4, 2, 5),
(4, 3, 5),
(4, 4, 5),
(4, 5, 5),
(4, 6, 5),
(4, 7, 5),

(5, 1, 5),
(5, 2, 5),
(5, 3, 5),
(5, 4, 5),
(5, 5, 5),
(5, 6, 5),
(5, 7, 5),

(6, 1, 5),
(6, 2, 5),
(6, 3, 5),
(6, 4, 5),
(6, 5, 5),
(6, 6, 5),
(6, 7, 5),

(7, 1, 5),
(7, 2, 5),
(7, 3, 5),
(7, 4, 5),
(7, 5, 5),
(7, 6, 5),
(7, 7, 5);");
$qr->execute();
?>