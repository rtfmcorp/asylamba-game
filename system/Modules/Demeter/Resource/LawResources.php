<?php

/**
 * ressources pour les lois
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Demeter
 * @update 29.09.14
*/
namespace Asylamba\Modules\Demeter\Resource;

#id des forums : < 10 = pour tous les gens d'une faction, >= 10 < 20 = pour le gouvernement d'une fac, >= 20 pour les chefs de toutes les factions
Class LawResources {

	private static $laws = array(
		array(
			'id' => 1,
			'bonusLaw' => FALSE,
			'devName' => 'sector taxes',
			'name' => 'Ajustement de l\'imposition sectorielle',
			'department' => 3,
			'undeterminedDuration' => FALSE,
			'price' => 50000,
			'bonus' => 0,
			'shortDescription' => 'Les planètes dans les secteurs sous votre contrôle vous paient un impôt et c\'est à la faction de choisir ce dernier.',
			'longDescription' => 'Cette loi modifie le taux d\'imposition appliqué aux planètes d\'un secteur en votre possession. La redevance perçue provient autant des bases de votre faction que de celles de factions ennemies.<br /><br />Un impôt faible dans vos secteurs faibles renforcera votre faction et un impôt fort dans les secteurs partagés permettra de prendre plus de crédits de joueurs ennemis.<br /><br />L\'impôt peut varier de 2% à 15%.',
			'image' => '',
			'isImplemented' => TRUE),
		array(
			'id' => 2,
			'bonusLaw' => FALSE,
			'devName' => 'secteurName',
			'name' => 'Décret de revendication',
			'department' => 6,
			'undeterminedDuration' => FALSE,
			'price' => 250000,
			'bonus' => 0,
			'shortDescription' => 'Afin de montrer à tous la puissance de votre faction et d\'asseoir la conquête d\'un nouveau territoire, ce décret permet de modifier le nom d\'un secteur.',
			'longDescription' => 'Permet de modifier le nom d\'un secteur que vous possédez.',
			'image' => '',
			'isImplemented' => TRUE),
		array(
			'id' => 3,
			'bonusLaw' => FALSE,
			'devName' => 'commercial taxes export',
			'name' => 'Ajustement des taxes de vente',
			'department' => 3,
			'undeterminedDuration' => FALSE,
			'price' => 50000,
			'bonus' => 0,
			'shortDescription' => 'Cette loi permet de modifier les taxes sur les ventes dans le marché. Elle permet de régler finement la taxe perçue par la faction lors de la vente d\'un bien en fonction de sa destination.',
			'longDescription' => 'Modifie le taux de la taxe à la vente de produit pour une faction précise.<br /><br />Un taux bas favorisera le commerce entre deux factions.<br /><br />L\'impôt peut varier de 2% à 15% entre deux factions différentes et de 0% à 15% au sein de la même faction.',
			'image' => '',
			'isImplemented' => TRUE),
		array(
			'id' => 4,
			'bonusLaw' => FALSE,
			'devName' => 'commercial taxes import',
			'name' => 'Ajustement des taxes d\'achat',
			'department' => 3,
			'undeterminedDuration' => FALSE,
			'price' => 50000,
			'bonus' => 0,
			'shortDescription' => 'Cette loi permet de modifier les taxes sur les achats dans le marché. Elle permet de régler finement la taxe perçue par la faction lors de l\'achat d\'un bien en fonction de sa provenance.',
			'longDescription' => 'Modifie le taux de la taxe à l\'achat de produit d\'une faction précise.<br /><br />Un taux bas favorisera le commerce entre deux factions.<br /><br />L\'impôt peut varier de 2% à 15% entre deux factions différentes et de 0% à 15% au sein de la même faction.',
			'image' => '',
			'isImplemented' => TRUE),
		array(
			'id' => 5,
			'bonusLaw' => TRUE,
			'devName' => 'military subvention',
			'name' => 'Effort de guerre',
			'department' => 4,
			'undeterminedDuration' => FALSE,
			'price' => 1000,
			'bonus' => 20,
			'shortDescription' => 'Lorsque la faction est menacée, concentrez vos forces dans la production de vaisseaux dans l\'espoir de chasser l\'envahisseur. Les autres pans de votre économie en souffriront certainement.',
			'longDescription' => 'La production des vaisseaux est accélérée de 20% mais la production de ressource diminue de 10%.<br /><br />Le prix de cette loi est indiqué pour une relève et varie en fonction du nombre de joueurs.',
			'image' => '',
			'isImplemented' => TRUE),
		array(
			'id' => 6,
			'bonusLaw' => TRUE,
			'devName' => 'technology',
			'name' => 'Transfert technologique',
			'department' => 5,
			'undeterminedDuration' => FALSE,
			'price' => 1250,
			'bonus' => 10,
			'shortDescription' => 'Donnez des subventions à vos centre de recherche et mettez sur pied un programme d\'échange d\'informations entre les membres de votre faction afin d\'améliorer les méthodes de travail de vos scientifiques.',
			'longDescription' => 'Cette loi offre à tous les membres de la faction un bonus dans le développement de leurs technologies de 10% (cumulé avec les autres bonus). <br /><br />Seules les recherches lancées lorsque le bonus est actif seront impactées.<br /><br />Le prix de cette loi est indiqué pour une relève et varie en fonction du nombre de joueurs.',
			'image' => '',
			'isImplemented' => TRUE),
		array(
			'id' => 7,
			'bonusLaw' => FALSE,
			'devName' => 'peace pact',
			'name' => 'Traité de paix',
			'department' => 6,
			'undeterminedDuration' => FALSE,
			'price' => 10000,
			'bonus' => 0,
			'shortDescription' => 'Un pacte de non-agression n\'est pas contraignant mais garantit que les membres des deux factions ne s\'attaquent pas entre eux. Les attaques violant ce pacte sont notifiées dans les registres de guerre de la faction. <br /><br />Ce type de pacte est limité à deux par faction.',
			'longDescription' => '',
			'image' => '',
			'isImplemented' => TRUE),
		array(
			'id' => 8,
			'bonusLaw' => FALSE,
			'devName' => 'war declaration',
			'name' => 'Déclaration de guerre',
			'department' => 6,
			'undeterminedDuration' => FALSE,
			'price' => 10000,
			'bonus' => 0,
			'shortDescription' => 'Lors de la déclaration de guerre, toutes les routes commerciales entre les deux factions belligérantes sont coupées. Durant toute la durée de la guerre, aucun lien commercial ne sera possible.',
			'longDescription' => 'Ce traité gèle les routes commerciales entre les deux factions concernées et empêche d\'en construire de nouvelles.',
			'image' => '',
			'isImplemented' => TRUE),
		array(
			'id' => 9,
			'bonusLaw' => FALSE,
			'devName' => 'total alliance',
			'name' => 'Alliance totale',
			'department' => 6,
			'undeterminedDuration' => FALSE,
			'price' => 10000,
			'bonus' => 0,
			'shortDescription' => 'Une alliance totale est un fort lien entre deux factions. Ce traité n\'est pas contraignant mais les attaques le violant sont notifiées dans les registres de guerre de la faction. <br /><br />Un seul pacte de ce type peut être rédigé par faction.',
			'longDescription' => '',
			'image' => '',
			'isImplemented' => TRUE),
		array(
			'id' => 10,
			'bonusLaw' => FALSE,
			'devName' => 'neutrality',
			'name' => 'Neutralité',
			'department' => 6,
			'undeterminedDuration' => FALSE,
			'price' => 10000,
			'bonus' => 0,
			'shortDescription' => 'Une faction n\'a ni affinités ni problèmes avec une autre faction neutre. De petits problèmes territoriaux peuvent exister sans remettre en cause ce statut.',
			'longDescription' => '',
			'image' => '',
			'isImplemented' => TRUE),
		array(
			'id' => 11,
			'bonusLaw' => FALSE,
			'devName' => 'punition',
			'name' => 'Amende',
			'department' => 5,
			'undeterminedDuration' => FALSE,
			'price' => 2000,
			'bonus' => 0,
			'shortDescription' => 'Permet d\'amender un joueur si celui-ci n\'obéit pas aux directives du gouvernement.',
			'longDescription' => 'Il n\'y a aucune limite quant à l\'utilisation de cette loi, vous pouvez en faire un usage abusif.',
			'image' => '',
			'isImplemented' => TRUE)
	);

	public static function getInfo($id, $info) {
		if ($id <= self::size()) {
			if (in_array($info, array('id', 'bonusLaw', 'devName', 'name', 'department', 'price', 'duration', 'bonus', 'shortDescription', 'longDescription', 'image', 'isImplemented'))) {
				return self::$laws[$id - 1][$info];
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	public static function size() { return count(self::$laws); }
}