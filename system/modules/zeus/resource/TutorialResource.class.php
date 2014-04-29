<?php

/**
 * TutorialResource
 *
 * @author Jacky Casas
 * @copyright Asylamba
 *
 * @package Zeus
 * @update 25.04.14
 */

class TutorialResource {

	const GENERATOR_LEVEL_2 = 1;
	const REFINERY_LEVEL_3 = 2;
	const REFINERY_MODE_PRODUCTION = 3;

	public static function stepExists($step) {
		if ($step > 0 AND $step <= count(self::$steps)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public static function isLastStep($step) {
		if ($step == count(self::$steps)) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public static function getInfo($id, $info) {
		if ($id <= count(self::$steps)) {
			if (in_array($info, array('id', 'title', 'description', 'experienceReward'))) {
				return self::$steps[$id - 1][$info];
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	private static $steps = array(
		array(
			'id' => 1,
			'title' => 'Construire le générateur au niveau 2',
			'description' => 'Pour construire le générateur, allez sur votre base orbitale en cliquant sur son nom en-haut à gauche de l\'écran. 
				Vous vous trouvez à présent sur la vue de situation. 
				A gauche de votre écran se trouve une barre de navigation, elle vous permet de vous déplacer dans les différents bâtiments. 
				Cliquez sur l\'icône du générateur pour y accéder.
				<br />
				Le générateur est le bâtiment qui permet de construire les autres bâtiments. Cliquez sur "augmenter vers le niveau 2" sur le générateur, celui-ci sera mis dans la file de construction et mettra un certain temps à se terminer.
				En survolant chaque bâtiment avec votre souris, un petit "+" apparaît. Si vous cliquez dessus, un tableau avec les prix et les temps de construction pour les différents niveaux apparaîtra.
				<br />
				Dans Asylamba, la navigation est horizontale, pour faire glisser le panneau central, utilisez les flèches directionnelles ou alors cliquez sur les flèches qui s\'affichent aux deux extrémités de l\'écran.',
			'experienceReward' => 3),
		array(
			'id' => 2,
			'title' => 'Construire la raffinerie au niveau 3',
			'description' => 'Rendez-vous à nouveau dans le générateur. Cette fois, vous devrez construire la Raffinerie au niveau 3. Si le bouton de construction est grisé, cela veut dire que vous n\'avez pas tous les prérequis pour exécuter la construction. 
				Il faut toujours que le niveau du générateur soit plus haut que le niveau des autres bâtiments, construisez donc un niveau supplémentaire du générateur.
				<br />
				La raffinerie sert à produire des ressources et à les stocker. Plus le niveau de la raffinerie est élevé, plus elle sera efficiente.
				Les ressources sont produites chaque relève. Une relève correspond à une heure.
				<br />
				Dans chaque bâtiment, il y a un panneau nommé "à propos". Si vous voulez en savoir plus, lisez ce panneau, des informations importantes et intéressantes peuvent s\'y trouver.',
			'experienceReward' => 10),
		array(
			'id' => 3,
			'title' => 'Mettre la raffinerie en mode production',
			'description' => 'Allez dans la raffinerie : sur votre base orbitale, puis cliquez sur l\'icône de la Raffinerie qui se trouve en troisième position de la barre de navigation rapide à gauche de l\'écran.
				Dans ce bâtiment, vous pouvez voir à tout instant combien vous produisez et ou en est votre stock.
				La Raffinerie possède 2 modes : le mode "Stockage" et le mode "Production". Le premier augmente la capacité de stockage et le second augmente la production horaire. 
				Essayer donc de passer en mode "Production" en cliquant sur le bouton prévu à cet effet.',
			'experienceReward' => 15)
	);
}
?>