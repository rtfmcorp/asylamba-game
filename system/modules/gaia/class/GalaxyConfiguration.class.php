<?php
abstract class GalaxyConfiguration {
	# general params
	public $galaxy = array(
		'size' => 250,
		'mask' => 10,
		'systemProportion' => array(3, 8, 9, 25, 55),
		'population' => array(700, 25000)
	);

	public $sectors = array(
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('105, 135', '115, 145', '135, 145', '145, 135', '145, 115', '135, 105', '115, 105', '105, 115'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('145, 115', '135, 105', '122, 105', '123, 84', '138, 83', '136, 90', '158, 92', '163, 83', '177, 100', '182, 107', '165, 129', '145, 127'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('145, 127', '165, 129', '181, 154', '185, 160', '156, 173', '122, 166', '123, 145', '135, 145', '145, 135'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('105, 127', '105, 135', '115, 145', '123, 145', '122, 166', '100, 170', '78, 165', '68, 145', '89, 128'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('105, 127', '89, 128', '65, 120', '60, 111', '65, 95', '92, 95', '101, 86', '111, 85', '123, 84', '122, 105', '115, 105', '105, 115'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('62, 68', '86, 82', '106, 69', '111, 85', '101, 86', '92, 95', '65, 95', '60, 111', '46, 85'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('62, 68', '60, 48', '71, 47', '69, 38', '90, 34', '107, 38', '100, 50', '106, 69', '86, 82'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('122, 36', '148, 48', '138, 83', '123, 84', '111, 85', '100, 50', '107, 38', '115, 39'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('158, 92', '136, 90', '148, 48', '185, 45', '198, 46', '196, 59', '178, 55', '163, 83'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('217, 100', '177, 100', '163, 83', '178, 55', '187, 86', '208, 82'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('182, 107', '194, 123', '209, 122', '209, 146', '181, 154', '165, 129'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('122, 166', '156, 173', '185, 160', '191, 168','195, 175', '190, 199', '172, 188', '138, 186'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('172, 188', '190, 199', '199, 204', '176, 215', '143, 207', '138, 186'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('103, 185', '138, 186', '143, 207', '125, 230', '101, 202'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('78, 165', '100, 170', '122, 166', '138, 186', '103, 185', '101, 202', '88, 204'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('70, 167', '59, 195', '35, 187', '27, 206', '48, 203', '72, 207', '88, 204', '78, 165'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('35, 138', '65, 120', '89, 128', '68, 145', '78, 165', '70, 167'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('0, 132', '0, 110', '25, 105', '60, 111', '65, 120', '35, 138'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('0, 110', '0, 87', '22, 82', '46, 85', '60, 111', '25, 105'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('0, 87', '0, 41', '35, 48', '60, 48', '62, 68', '46, 85', '22, 82'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('0, 0', '0, 41', '35, 48', '60, 48', '71, 47', '69, 38', '67, 30', '73, 10', '65, 0'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('65, 0', '113, 0', '135, 28', '122, 36', '115, 39', '107, 38', '90, 34', '69, 38', '67, 30', '73, 10'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('113, 0', '185, 0', '180, 19', '185, 45', '148, 48', '122, 36', '135, 28'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('185, 0', '250, 0', '250, 40', '230, 49', '198, 46', '185, 45', '180, 19'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('250, 40', '250, 85', '235, 77', '208, 82', '187, 86', '178, 55', '196, 59', '198, 46', '230, 49'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('250, 85', '250, 100', '217, 100', '208, 82', '235, 77'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('250, 100', '250, 120', '209, 122', '194, 123', '182, 107', '177, 100', '217, 100'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('250, 120', '250, 162', '191, 168', '185, 160', '181, 154', '209, 146', '209, 122'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('250, 162', '250, 202', '199, 204', '190, 199', '195, 175', '191, 168'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('250, 202', '250, 250', '181, 250', '176, 215', '199, 204'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('181, 250', '123, 250', '125, 230', '143, 207', '176, 215'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('123, 250', '73, 250', '72, 207', '88, 204', '101, 202', '125, 230'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('73, 250', '0, 250', '0, 210', '27, 206', '48, 203', '72, 207'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('0, 180', '70, 167', '59, 195', '35, 187', '27, 206', '0, 210'),
			'barycentre' => NULL,
			'display' => NULL
		),
		array(
			'id' => 1,
			'beginColor' => 0,
			'vertices' =>array('0, 132', '35, 138', '70, 167', '0, 180'),
			'barycentre' => NULL,
			'display' => NULL
		)
	);

	public $systems = array(
		array(
			'id' => 1,
			'name' => 'ruine',
			'placesPropotion' => array(0, 0, 80, 10, 0, 10),
			'nbrPlaces' => array(1, 5)
		),
		array(
			'id' => 2,
			'name' => 'nébuleuse',
			'placesPropotion' => array(5, 5, 10, 75, 0, 5),
			'nbrPlaces' => array(2, 8)
		),
		array(
			'id' => 3,
			'name' => 'géante bleue',
			'placesPropotion' => array(40, 25, 10, 0, 20, 5),
			'nbrPlaces' => array(12, 18)
		),
		array(
			'id' => 4,
			'name' => 'naine jaune',
			'placesPropotion' => array(55, 20, 5, 0, 15, 5),
			'nbrPlaces' => array(6, 12)
		),
		array(
			'id' => 5,
			'name' => 'naine rouge',
			'placesPropotion' => array(70, 10, 5, 0, 10, 5),
			'nbrPlaces' => array(2, 6)
		)
	);

	public $places = array(
		array(
			'id' => 1,
			'name' => 'planète tellurique',
			'resources' => array(45, 55),
			'history' => array(20, 40),
		),
		array(
			'id' => 2,
			'name' => 'planète gazeuse',
			'resources' => array(60, 80),
			'history' => array(0, 10),
		),
		array(
			'id' => 3,
			'name' => 'ruine',
			'resources' => array(30, 40),
			'history' => array(90, 100),
		),
		array(
			'id' => 4,
			'name' => 'poches de gaz',
			'resources' => array(80, 100),
			'history' => array(0, 25),
		),
		array(
			'id' => 5,
			'name' => 'ceinture d\'astéroides',
			'resources' => array(80, 100),
			'history' => array(10, 25),
		),
		array(
			'id' => 6,
			'name' => 'lieu vide',
			'resources' => array(0, 0),
			'history' => array(0, 0),
		)
	);

	# display params
	public $scale = 20;
}
?>