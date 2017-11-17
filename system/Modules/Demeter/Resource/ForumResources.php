<?php

/**
 * ressources pour les foruuüm
 *
 * @author Noé Zufferey
 * @copyright Expansion - le jeu
 *
 * @package Demeter
 * @update 06.10.13
*/
namespace Asylamba\Modules\Demeter\Resource;

#id des forums : < 10 = pour tous les gens d'une faction, >= 10 < 20 = pour le gouvernement d'une fac, >= 20 pour les chefs de toutes les factions
class ForumResources
{
    private static $idLink = array(1, 2, 3, 4, 5, 10, 20);

    private static $forums = array(
        array(
            'id' => 1,
            'devName' => 'war',
            'name' => 'Plans de bataille',
            'shortDescription' => 'Planifiez vos batailles, écrasez vos ennemis',
            'longDescription' =>'',
            'image' => ''),
        array(
            'id' => 2,
            'devName' => 'noob',
            'name' => 'Commerce',
            'shortDescription' => 'Proposition de route commerciale, offre sur le marché',
            'longDescription' =>'',
            'image' => ''),
        array(
            'id' => 3,
            'devName' => 'politic',
            'name' => 'Politique',
            'shortDescription' => 'Présentez votre programme, votre vision de l\'avenir',
            'longDescription' =>'',
            'image' => ''),
        array(
            'id' => 4,
            'devName' => 'flood',
            'name' => 'Biastro',
            'shortDescription' => 'Buvez de la bière et racontez n\'importe quoi',
            'longDescription' =>'',
            'image' => ''),
        array(
            'id' => 5,
            'devName' => 'op',
            'name' => 'Opération Spéciale',
            'shortDescription' => 'Pour les guerres éclaires ou pour une défense efficace',
            'longDescription' =>'',
            'image' => ''),
        array(
            'id' => 10,
            'devName' => 'government',
            'name' => 'Salle du Conseil',
            'shortDescription' => 'Ici se déroulent les débats du conseil restreint de la faction',
            'longDescription' =>'',
            'image' => ''),
        array(
            'id' => 20,
            'devName' => 'chiefes',
            'name' => 'Ambassade',
            'shortDescription' => 'Seuls les chefs de chaque faction ont accès à l\'Ambassade',
            'longDescription' =>'',
            'image' => ''),
        array(
            'id' => 30,
            'devName' => 'campaign',
            'name' => 'campaign',
            'shortDescription' => '',
            'longDescription' =>'',
            'image' => '')
    );

    public static function getInfo($id, $info)
    {
        if ($id <= self::size()) {
            if (in_array($info, array('id', 'devName', 'name', 'shortDescription', 'longDescription', 'image'))) {
                return self::$forums[$id - 1][$info];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function getInfoForId($id, $info)
    {
        if (in_array($info, array('id', 'devName', 'name', 'shortDescription', 'longDescription', 'image'))) {
            $tmp = array_keys(self::$idLink, $id);
            return self::$forums[$tmp[0]][$info];
        } else {
            return false;
        }
    }

    public static function size()
    {
        return count(self::$forums);
    }
}
