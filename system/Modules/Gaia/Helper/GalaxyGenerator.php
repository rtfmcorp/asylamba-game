<?php

namespace Asylamba\Modules\Gaia\Helper;

use Asylamba\Classes\Database\DatabaseAdmin;
use Asylamba\Classes\Library\Utils;

use Asylamba\Modules\Gaia\Galaxy\GalaxyConfiguration;

use Asylamba\Modules\Gaia\Model\PointLocation;
use Asylamba\Modules\Gaia\Model\Place;
use Asylamba\Classes\Library\Format;

class GalaxyGenerator
{
    const MAX_QUERY = 5000;

    # stats
    public $nbSystem = 0;
    public $listSystem = array();

    public $nbPlace = 0;
    public $popTotal = 0;
    public $listPlace = array();

    public $nbSector = 0;
    public $systemDeleted = 0;
    public $listSector = array();

    /** @var string **/
    protected $output;
    /** @var DatabaseAdmin **/
    protected $databaseAdmin;
    /** @var GalaxyConfiguration **/
    protected $galaxyConfiguration;
    
    /**
     * @param DatabaseAdmin $databaseAdmin
     * @param GalaxyConfiguration $galaxyConfiguration
     */
    public function __construct(DatabaseAdmin $databaseAdmin, GalaxyConfiguration $galaxyConfiguration)
    {
        $this->databaseAdmin = $databaseAdmin;
        $this->galaxyConfiguration = $galaxyConfiguration;
    }

    public function generate()
    {
        $this->clear();

        # generation
        $this->generateSector();
        $this->generateSystem();
        $this->associateSystemToSector();
        $this->generatePlace();

        $this->save();

        $this->getStatisticsSector();
    }

    public function clear()
    {
        $this->databaseAdmin->query('SET FOREIGN_KEY_CHECKS = 0;');
        
        $this->databaseAdmin->query('TRUNCATE place');
        $this->log('table `place` vidées');

        $this->databaseAdmin->query('TRUNCATE system');
        $this->log('table `system` vidées');

        $this->databaseAdmin->query('TRUNCATE sector');
        $this->log('table `sector` vidées');

        $this->databaseAdmin->query('SET FOREIGN_KEY_CHECKS = 1;');
        $this->log('_ _ _ _');
    }

    public function save()
    {
        # clean up database
        $this->clear();

        $this->log('sauvegarde des secteurs');
        for ($i = 0; $i < ceil(count($this->listSector) / self::MAX_QUERY); $i++) {
            $qr = 'INSERT INTO sector(id, rColor, xPosition, yPosition, xBarycentric, yBarycentric, tax, population, lifePlanet, name, prime, points) VALUES ';
            
            for ($j = $i * self::MAX_QUERY; $j < (($i + 1) * self::MAX_QUERY) - 1; $j++) {
                if (isset($this->listSector[$j])) {
                    $qr .= '(\'' . implode('\', \'', $this->listSector[$j]) . '\'), ';
                }
            }

            $qr = substr($qr, 0, -2);
            $this->databaseAdmin->query($qr);
        }
        $this->log(ceil(count($this->listSector) / self::MAX_QUERY) . ' requêtes `INSERT`');

        $this->log('sauvegarde des systèmes');
        for ($i = 0; $i < ceil(count($this->listSystem) / self::MAX_QUERY); $i++) {
            $qr = 'INSERT INTO system(id, rSector, rColor, xPosition, yPosition, typeOfSystem) VALUES ';
            
            for ($j = $i * self::MAX_QUERY; $j < (($i + 1) * self::MAX_QUERY) - 1; $j++) {
                if (isset($this->listSystem[$j])) {
                    $qr .= '(' . implode(', ', $this->listSystem[$j]) . '), ';
                }
            }

            $qr = substr($qr, 0, -2);
            $this->databaseAdmin->query($qr);
        }
        $this->log(ceil(count($this->listSystem) / self::MAX_QUERY) . ' requêtes `INSERT`');

        $this->log('sauvegarde des places');
        for ($i = 0; $i < ceil(count($this->listPlace) / self::MAX_QUERY); $i++) {
            $qr = 'INSERT INTO place(id, rSystem, typeOfPlace, position, population, coefResources, coefHistory, resources, danger, maxDanger, uPlace) VALUES ';
            
            for ($j = $i * self::MAX_QUERY; $j < (($i + 1) * self::MAX_QUERY) - 1; $j++) {
                if (isset($this->listPlace[$j])) {
                    $qr .= '(' . implode(', ', $this->listPlace[$j]) . ', "' . Utils::addSecondsToDate(Utils::now(), -259200) . '"), ';
                }
            }

            $qr = substr($qr, 0, -2);
            $this->databaseAdmin->query($qr);
        }
        $this->log(ceil(count($this->listPlace) / self::MAX_QUERY) . ' requêtes `INSERT`');

        $this->log('_ _ _ _');
    }

    public function getLog()
    {
        $rt  = '<pre style="font-family: consolas;">';
        $rt .= $this->output;
        $rt .= '</pre>';

        return $rt;
    }

    private function log($text)
    {
        $this->output .= ">_ $text <br />";
    }

    private function generateSystem()
    {
        $this->log('génération des systèmes');

        # id
        $k = 1;

        # GENERATION DES LINES
        for ($w = 0; $w < count($this->galaxyConfiguration->galaxy['lineSystemPosition']); $w++) {
            # line point
            $xA = $this->galaxyConfiguration->galaxy['lineSystemPosition'][$w][0][0];
            $yA = $this->galaxyConfiguration->galaxy['lineSystemPosition'][$w][0][1];

            $xB = $this->galaxyConfiguration->galaxy['lineSystemPosition'][$w][1][0];
            $yB = $this->galaxyConfiguration->galaxy['lineSystemPosition'][$w][1][1];

            $l  = sqrt(pow($xB - $xA, 2) + pow($yB - $yA, 2));

            for ($i = 1; $i <= $this->galaxyConfiguration->galaxy['size']; $i++) {
                for ($j = 1; $j <= $this->galaxyConfiguration->galaxy['size']; $j++) {
                    # current cursor position
                    $xC = $j;
                    $yC = $i;

                    $d  = $this->distToSegment($xC, $yC, $xA, $yA, $xB, $yB);

                    $thickness = $this->galaxyConfiguration->galaxy['lineSystemPosition'][$w][2];
                    $intensity = $this->galaxyConfiguration->galaxy['lineSystemPosition'][$w][3];

                    if ($d < $this->galaxyConfiguration->galaxy['lineSystemPosition'][$w][2]) {
                        #$prob = rand(0, $this->galaxyConfiguration->galaxy['lineSystemPosition'][$w][3]);
                        $prob = rand(0, 100);


                        #if ($this->galaxyConfiguration->galaxy['lineSystemPosition'][$w][2] - $d > $prob) {
                        if (round($intensity - ($d * $intensity / $thickness)) >= $prob) {
                            $type = $this->getSystem();

                            $this->nbSystem++;
                            $this->listSystem[] = array($k, 0, 0, $xC, $yC, $type);

                            $k++;
                        }
                    }
                }
            }
        }

        # GENERATION DES ANNEAUX (circleSystemPosition)
        for ($w = 0; $w < count($this->galaxyConfiguration->galaxy['circleSystemPosition']); $w++) {
            # line point
            $xC = $this->galaxyConfiguration->galaxy['circleSystemPosition'][$w][0][0];
            $yC = $this->galaxyConfiguration->galaxy['circleSystemPosition'][$w][0][1];

            $radius    = $this->galaxyConfiguration->galaxy['circleSystemPosition'][$w][1];
            $thickness    = $this->galaxyConfiguration->galaxy['circleSystemPosition'][$w][2];
            $intensity    = $this->galaxyConfiguration->galaxy['circleSystemPosition'][$w][3];

            for ($i = 1; $i <= $this->galaxyConfiguration->galaxy['size']; $i++) {
                for ($j = 1; $j <= $this->galaxyConfiguration->galaxy['size']; $j++) {
                    # current cursor position
                    $xPosition = $j;
                    $yPosition = $i;

                    # calcul de la distance entre la case et le centre
                    $d = sqrt(
                        pow(abs($xC - $xPosition), 2) +
                        pow(abs($yC - $yPosition), 2)
                    );
    
                    if ($d >= ($radius - $thickness) && $d <= ($radius + $thickness)) {
                        $dtoseg = abs($d - $radius);
                        $prob    = rand(0, 100);

                        if (round($intensity - ($dtoseg * $intensity / $thickness)) >= $prob) {
                            $type = $this->getSystem();

                            $this->nbSystem++;
                            $this->listSystem[] = array($k, 0, 0, $xPosition, $yPosition, $type);

                            $k++;
                        }
                    }
                }
            }
        }

        # GENERATION PAR VAGUES
        if ($this->galaxyConfiguration->galaxy['systemPosition'] !== null) {
            for ($i = 1; $i <= $this->galaxyConfiguration->galaxy['size']; $i++) {
                for ($j = 1; $j <= $this->galaxyConfiguration->galaxy['size']; $j++) {
                    # current cursor position
                    $xPosition = $j;
                    $yPosition = $i;
                    
                    # calcul de la distance entre la case et le centre
                    $d2o = sqrt(
                        pow(abs(($this->galaxyConfiguration->galaxy['size'] / 2) - $xPosition), 2) +
                        pow(abs(($this->galaxyConfiguration->galaxy['size'] / 2) - $yPosition), 2)
                    );
                    
                    if ($this->isPointInMap($d2o)) {
                        $type = $this->getSystem();

                        $this->nbSystem++;
                        $this->listSystem[] = array($k, 0, 0, $xPosition, $yPosition, $type);

                        $k++;
                    }
                }
            }
        }

        $this->log($this->nbSystem . ' systèmes générés');
        $this->log('_ _ _ _');
    }

    public function generatePlace()
    {
        $this->log('génération des places');
        $k = 1;

        foreach ($this->listSystem as $system) {
            $sectorDanger = 0;
            foreach ($this->galaxyConfiguration->sectors as $sector) {
                if ($system[1] == $sector['id']) {
                    $sectorDanger = $sector['danger'];
                    break;
                }
            }

            $place = $this->getNbOfPlace($system[5]);

            for ($i = 0; $i < $place; $i++) {
                $type = $this->getTypeOfPlace($system[5]);

                if ($type == 1) {
                    $pointsRep = rand(1, 10);
                    $abilities = [
                        'population' => 0,
                        'history' => 0,
                        'resources' => 0
                    ];

                    # nombre de point a distribuer
                    if ($pointsRep < 2) {
                        $pointsTot = rand(90, 100);
                    } elseif ($pointsRep < 10) {
                        $pointsTot = 100;
                    } else {
                        $pointsTot = rand(100, 120);
                    }

                    # brassage du tableau
                    Utils::shuffle($abilities);

                    # répartition
                    $z = 1;
                    foreach ($abilities as $l => $v) {
                        if ($z < 3) {
                            $max = $pointsTot - ($z * 10);
                            $max = $max < 10 ? 10 : $max;

                            $points = rand(10, $max);
                            $abilities[$l] = $points;
                            $pointsTot -= $points;
                        } else {
                            $abilities[$l] = $pointsTot < 5 ? 5 : $pointsTot;
                        }

                        $z++;
                    }

                    $population = $abilities['population'] * 250 / 100;
                    $history    = $abilities['history'];
                    $resources    = $abilities['resources'];
                    $stRES        = 0;
                } elseif ($type == 6) {
                    $population = 0;
                    $history    = 0;
                    $resources    = 0;
                    $stRES        = 0;
                } else {
                    $population = $this->galaxyConfiguration->places[$type - 1]['credits'];
                    $resources    = $this->galaxyConfiguration->places[$type - 1]['resources'];
                    $history    = $this->galaxyConfiguration->places[$type - 1]['history'];
                    $stRES        = rand(2000000, 20000000);
                }

                # TODO DANGER
                switch ($sectorDanger) {
                    case GalaxyConfiguration::DNG_CASUAL:
                        $danger = rand(0, Place::DNG_CASUAL);
                    break;
                    case GalaxyConfiguration::DNG_EASY:
                        $danger = rand(3, Place::DNG_EASY);
                    break;
                    case GalaxyConfiguration::DNG_MEDIUM:
                        $danger = rand(6, Place::DNG_MEDIUM);
                    break;
                    case GalaxyConfiguration::DNG_HARD:
                        $danger = rand(9, Place::DNG_HARD);
                    break;
                    case GalaxyConfiguration::DNG_VERY_HARD:
                        $danger = rand(12, Place::DNG_VERY_HARD);
                    break;
                    default: $danger = 0; break;
                }

                $this->nbPlace++;
                $this->popTotal += $population;
                $this->listPlace[] = array($k, $system[0], $type, ($i + 1), $population, $resources, $history, $stRES, $danger, $danger);
                $k++;
            }
        }

        $this->log($this->nbPlace . ' places générées');
        $this->log(Format::numberFormat($this->popTotal * 1000000) . ' de population');
        $this->log('_ _ _ _');
    }

    public function generateSector()
    {
        $this->log('génération des secteurs');
        $k = 1;

        foreach ($this->galaxyConfiguration->sectors as $sector) {
            $this->nbSector++;

            $prime = ($sector['beginColor'] != 0)
                ? 1
                : 0;

            $this->listSector[] = array(
                $k,
                $sector['beginColor'],
                $sector['display'][0],
                $sector['display'][1],
                $sector['barycentre'][0],
                $sector['barycentre'][1],
                5,
                0,
                0,
                $sector['name'],
                $prime,
                $sector['points']
            );

            $k++;
        }

        $this->log($this->nbSector . ' secteurs générés');
        $this->log('_ _ _ _');
    }

    public function associateSystemToSector()
    {
        $pl = new PointLocation();
        $systemToDelete = array();
        $k = 0;

        foreach ($this->listSystem as $v) {
            foreach ($this->galaxyConfiguration->sectors as $w) {
                $place = $pl->pointInPolygon($v[3] . ', ' . $v[4], $w['vertices']);

                if ($place == 1 or $place == 2) {
                    $systemToDelete[] = $v[0];
                    break;
                } elseif ($place == 3) {
                    $this->listSystem[$k][1] = $w['id'];
                    break;
                }
            }
            $k++;
        }

        foreach ($this->listSystem as $v) {
            if ($v[1] == 0) {
                $systemToDelete[] = $v[0];
            }
        }
        
        # suppression des systemes sur des lignes ou des angles
        for ($i = count($this->listSystem) - 1; $i >= 0; $i--) {
            if (in_array($this->listSystem[$i][0], $systemToDelete)) {
                unset($this->listSystem[$i]);
            }
        }

        $this->systemDeleted = count($systemToDelete);
    }

    protected function getStatisticsSector()
    {
        foreach ($this->listSector as $sector) {
            $id = $sector[0];

            $qr = $this->databaseAdmin->prepare('SELECT
					COUNT(pl.id) AS planet,
					SUM(pl.population) AS population
				FROM sector AS se
				LEFT JOIN system AS sy
					ON se.id = sy.rSector
				LEFT JOIN place AS pl
					ON sy.id = pl.rSystem
				WHERE pl.typeOfPlace = 1
				AND se.id = ?');
            $qr->execute(array($id));
            $aw = $qr->fetch();

            $nbrPlanet = $aw['planet'];
            $population = ceil($aw['population']);

            $qr->closeCursor();

            $qr = $this->databaseAdmin->prepare('UPDATE sector SET lifePlanet = ?, population = ? WHERE id = ?');
            $qr->execute(array($nbrPlanet, $population, $id));

            $qr->closeCursor();
        }
    }

    protected function isPointInMap($d2o)
    {
        $mask = rand(1, $this->galaxyConfiguration->galaxy['mask']);

        if ($mask < 3) {
            $realPosition = $this->galaxyConfiguration->galaxy['diag'] - $d2o;
            $step          = $this->galaxyConfiguration->galaxy['diag'] / count($this->galaxyConfiguration->galaxy['systemPosition']);
            $currentStep  = floor($realPosition / $step);

            $random = rand(0, 100);

            if ($this->galaxyConfiguration->galaxy['systemPosition'][$currentStep] > $random) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    protected function l2p($x1, $x2, $y1, $y2)
    {
        return (pow($x1 - $y1, 2) + pow($x2 - $y2, 2));
    }

    protected function distToSegment($p1, $p2, $v1, $v2, $w1, $w2)
    {
        $l2 = $this->l2p($v1, $v2, $w1, $w2);

        if ($l2 == 0) {
            return sqrt($this->l2p($p1, $p2, $v1, $v2));
        }

        $t  = (($p1 - $v1) * ($w1 - $v1) + ($p2 - $v2) * ($w2 - $v2)) / $l2;

        if ($t < 0) {
            return sqrt($this->l2p($p1, $p2, $v1, $v2));
        }

        if ($t > 1) {
            return sqrt($this->l2p($p1, $p2, $w1, $w2));
        }

        $tx = $v1 + $t * ($w1 - $v1);
        $ty = $v2 + $t * ($w2 - $v2);

        return sqrt($this->l2p($p1, $p2, $tx, $ty));
    }

    protected function getProportion($params, $value)
    {
        $cursor    = 0;
        $type    = 0;
        $min    = 0;
        $max    = 0;

        for ($i = 0; $i < count($params); $i++) {
            if ($i == 0) {
                $max = $params[$i];
            } elseif ($i < count($params) - 1) {
                $min = $cursor;
                $max = $cursor + $params[$i];
            } else {
                $min = $cursor;
                $max = 100;
            }

            $cursor = $max;
            $type += 1;


            if ($value > $min && $value <= $max) {
                return $type;
            }
        }
    }

    protected function getSystem()
    {
        return $this->getProportion($this->galaxyConfiguration->galaxy['systemProportion'], rand(1, 100));
    }

    protected function getNbOfPlace($systemType)
    {
        return rand(
            $this->galaxyConfiguration->systems[$systemType - 1]['nbrPlaces'][0],
            $this->galaxyConfiguration->systems[$systemType - 1]['nbrPlaces'][1]
        );
    }

    protected function getTypeOfPlace($systemType)
    {
        return $this->getProportion($this->galaxyConfiguration->systems[$systemType - 1]['placesPropotion'], rand(1, 100));
    }
}
