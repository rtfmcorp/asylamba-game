<?php

namespace Asylamba\Modules\Gaia\Galaxy;

class GalaxyConfigurationV2 extends GalaxyConfiguration
{
    public $galaxy = [
        'size' => 250,
        'diag' => 177,
        'mask' => 15,
        'systemProportion'    => [3, 8, 9, 25, 55],
        'systemPosition'    => null,
        'lineSystemPosition' => [
        #	[[pA], [pB], EPAISSEUR, INTENSITE],
            [[20, 170], [40, 118], 12, 8],
            [[80, 230], [137, 210], 12, 8],
        ],
        'circleSystemPosition' => [
        #	[[X1], RAYON, EPAISSEUR, INTENSITE],
            [[-50, 300], 70, 20, 9],
            [[-50, 300], 90, 20, 9],
            [[-50, 300], 110, 20, 9],
            [[-50, 300], 130, 20, 9],

            [[-50, 300], 270, 65, 10],
        ],
        'population' => [700, 25000],
    ];

    public $sectors = [
        [
            'id' => 1,
            'beginColor' => 1,
            'vertices' => [0, 250, 0, 220, 25, 215, 50, 220, 35, 235, 30, 250],
            'barycentre' => [23, 232],
            'display' => [23, 232],
            'name' => 'Secteur 1',
            'danger' => self::DNG_CASUAL,
            'points' => 1
        ], [
            'id' => 2,
            'beginColor' => 1,
            'vertices' => [30, 250, 35, 235, 50, 220, 60, 190, 75, 215, 65, 250],
            'barycentre' => [53, 227],
            'display' => [53, 227],
            'name' => 'Secteur 2',
            'danger' => self::DNG_CASUAL,
            'points' => 1
        ], [
            'id' => 3,
            'beginColor' => 1,
            'vertices' => [0, 220, 0, 195, 40, 180, 60, 190, 50, 220, 25, 215],
            'barycentre' => [29, 203],
            'display' => [29, 195],
            'name' => 'Secteur 3',
            'danger' => self::DNG_CASUAL,
            'points' => 1
        ], [
            'id' => 4,
            'beginColor' => 0,
            'vertices' => [0, 195, 0, 155, 15, 145, 25, 155, 40, 155, 40, 180],
            'barycentre' => [20, 164],
            'display' => [20, 164],
            'name' => 'Secteur 4',
            'danger' => self::DNG_EASY,
            'points' => 1
        ], [
            'id' => 5,
            'beginColor' => 0,
            'vertices' => [15, 145, 30, 145, 40, 155, 25, 155],
            'barycentre' => [28, 150],
            'display' => [25, 147],
            'name' => 'Secteur 5',
            'danger' => self::DNG_HARD,
            'points' => 2
        ], [
            'id' => 6,
            'beginColor' => 0,
            'vertices' => [15, 145, 25, 115, 55, 125, 40, 155, 30, 145],
            'barycentre' => [33, 137],
            'display' => [33, 130],
            'name' => 'Secteur 6',
            'danger' => self::DNG_MEDIUM,
            'points' => 1
        ], [
            'id' => 7,
            'beginColor' => 0,
            'vertices' => [0, 105, 0, 85, 25, 85, 30, 95, 70, 105, 75, 120, 55, 125, 25, 115],
            'barycentre' => [35, 104],
            'display' => [35, 104],
            'name' => 'Secteur 7',
            'danger' => self::DNG_MEDIUM,
            'points' => 1
        ], [
            'id' => 8,
            'beginColor' => 0,
            'vertices' => [25, 85, 40, 70, 50, 85, 30, 95],
            'barycentre' => [36, 84],
            'display' => [36, 78],
            'name' => 'Secteur 8',
            'danger' => self::DNG_HARD,
            'points' => 2
        ], [
            'id' => 9,
            'beginColor' => 0,
            'vertices' => [0, 85, 0, 55, 25, 50, 40, 50, 40, 70, 25, 85],
            'barycentre' => [22, 66],
            'display' => [18, 66],
            'name' => 'Secteur 9',
            'danger' => self::DNG_EASY,
            'points' => 1
        ], [
            'id' => 10,
            'beginColor' => 8,
            'vertices' => [0, 55, 0, 0, 50, 0, 55, 15, 50, 35, 40, 25, 20, 30, 25, 50],
            'barycentre' => [30, 26],
            'display' => [11, 11],
            'name' => 'Secteur 10',
            'danger' => self::DNG_CASUAL,
            'points' => 1
        ], [
            'id' => 11,
            'beginColor' => 8,
            'vertices' => [25, 50, 20, 30, 40, 25, 50, 35, 40, 50],
            'barycentre' => [35, 38],
            'display' => [30, 33],
            'name' => 'Secteur 11',
            'danger' => self::DNG_CASUAL,
            'points' => 2
        ], [
            'id' => 12,
            'beginColor' => 0,
            'vertices' => [50, 35, 55, 15, 50, 0, 85, 0, 85, 30, 75, 40],
            'barycentre' => [67, 20],
            'display' => [67, 16],
            'name' => 'Secteur 12',
            'danger' => self::DNG_EASY,
            'points' => 1
        ], [
            'id' => 13,
            'beginColor' => 0,
            'vertices' => [40, 70, 40, 50, 50, 35, 75, 40, 50, 85],
            'barycentre' => [51, 56],
            'display' => [51, 50],
            'name' => 'Secteur 13',
            'danger' => self::DNG_MEDIUM,
            'points' => 1
        ], [
            'id' => 14,
            'beginColor' => 0,
            'vertices' => [30, 95, 50, 85, 75, 40, 90, 60, 70, 105],
            'barycentre' => [63, 77],
            'display' => [63, 77],
            'name' => 'Secteur 14',
            'danger' => self::DNG_MEDIUM,
            'points' => 1
        ], [
            'id' => 15,
            'beginColor' => 0,
            'vertices' => [75, 40, 85, 30, 100, 50, 90, 60],
            'barycentre' => [88, 45],
            'display' => [83, 40],
            'name' => 'Secteur 15',
            'danger' => self::DNG_HARD,
            'points' => 2
        ], [
            'id' => 16,
            'beginColor' => 0,
            'vertices' => [85, 30, 85, 0, 110, 0, 130, 60, 100, 50],
            'barycentre' => [105, 35],
            'display' => [100, 20],
            'name' => 'Secteur 16',
            'danger' => self::DNG_MEDIUM,
            'points' => 1
        ], [
            'id' => 17,
            'beginColor' => 0,
            'vertices' => [130, 75, 130, 60, 110, 0, 115, 0, 160, 30, 135, 75],
            'barycentre' => [130, 40],
            'display' => [135, 30],
            'name' => 'Secteur 17',
            'danger' => self::DNG_EASY,
            'points' => 1
        ], [
            'id' => 18,
            'beginColor' => 0,
            'vertices' => [70, 105, 90, 60, 100, 50, 130, 60, 130, 75, 110, 75],
            'barycentre' => [105, 71],
            'display' => [100, 60],
            'name' => 'Secteur 18',
            'danger' => self::DNG_MEDIUM,
            'points' => 1
        ], [
            'id' => 19,
            'beginColor' => 0,
            'vertices' => [75, 120, 70, 105, 80, 110, 85, 120],
            'barycentre' => [78, 114],
            'display' => [74, 110],
            'name' => 'Secteur 19',
            'danger' => self::DNG_VERY_HARD,
            'points' => 5
        ], [
            'id' => 20,
            'beginColor' => 0,
            'vertices' => [55, 125, 75, 120, 85, 120, 110, 110, 85, 155],
            'barycentre' => [82, 126],
            'display' => [78, 130],
            'name' => 'Secteur 20',
            'danger' => self::DNG_MEDIUM,
            'points' => 1
        ], [
            'id' => 21,
            'beginColor' => 0,
            'vertices' => [70, 105, 110, 75, 115, 95, 110, 110, 85, 120, 80, 110],
            'barycentre' => [95, 103],
            'display' => [95, 93],
            'name' => 'Secteur 21',
            'danger' => self::DNG_MEDIUM,
            'points' => 1
        ], [
            'id' => 22,
            'beginColor' => 0,
            'vertices' => [110, 110, 115, 95, 130, 100, 130, 105],
            'barycentre' => [121, 103],
            'display' => [115, 98],
            'name' => 'Secteur 22',
            'danger' => self::DNG_HARD,
            'points' => 2
        ], [
            'id' => 23,
            'beginColor' => 0,
            'vertices' => [115, 95, 110, 75, 130, 75, 135, 75, 145, 95, 160, 115, 130, 130, 110, 110, 130, 105, 130, 100],
            'barycentre' => [130, 98],
            'display' => [137, 103],
            'name' => 'Secteur 23',
            'danger' => self::DNG_MEDIUM,
            'points' => 1
        ], [
            'id' => 24,
            'beginColor' => 4,
            'vertices' => [135, 75, 160, 30, 175, 40, 160, 90, 145, 95],
            'barycentre' => [155, 66],
            'display' => [150, 66],
            'name' => 'Secteur 24',
            'danger' => self::DNG_CASUAL,
            'points' => 1
        ], [
            'id' => 25,
            'beginColor' => 4,
            'vertices' => [160, 90, 175, 40, 210, 75, 175, 100],
            'barycentre' => [180, 76],
            'display' => [177, 70],
            'name' => 'Secteur 25',
            'danger' => self::DNG_CASUAL,
            'points' => 1
        ], [
            'id' => 26,
            'beginColor' => 4,
            'vertices' => [130, 130, 160, 115, 145, 95, 160, 90, 175, 100, 210, 75, 185, 120, 140, 140],
            'barycentre' => [163, 108],
            'display' => [170, 108],
            'name' => 'Secteur 26',
            'danger' => self::DNG_CASUAL,
            'points' => 1
        ], [
            'id' => 27,
            'beginColor' => 0,
            'vertices' => [85, 155, 110, 110, 130, 130, 95, 165],
            'barycentre' => [105, 140],
            'display' => [105, 133],
            'name' => 'Secteur 27',
            'danger' => self::DNG_MEDIUM,
            'points' => 1
        ], [
            'id' => 28,
            'beginColor' => 0,
            'vertices' => [95, 165, 130, 130, 140, 140, 130, 165, 125, 170],
            'barycentre' => [124, 154],
            'display' => [117, 154],
            'name' => 'Secteur 28',
            'danger' => self::DNG_MEDIUM,
            'points' => 1
        ], [
            'id' => 29,
            'beginColor' => 0,
            'vertices' => [95, 165, 125, 170, 130, 180, 140, 180, 125, 195],
            'barycentre' => [123, 178],
            'display' => [117, 178],
            'name' => 'Secteur 29',
            'danger' => self::DNG_MEDIUM,
            'points' => 1
        ], [
            'id' => 30,
            'beginColor' => 0,
            'vertices' => [125, 170, 130, 165, 140, 170, 140, 180, 130, 180],
            'barycentre' => [133, 173],
            'display' => [130, 170],
            'name' => 'Secteur 30',
            'danger' => self::DNG_VERY_HARD,
            'points' => 5
        ], [
            'id' => 31,
            'beginColor' => 0,
            'vertices' => [130, 165, 140, 140, 185, 120, 185, 135, 190, 160, 140, 170],
            'barycentre' => [162, 148],
            'display' => [162, 148],
            'name' => 'Secteur 31',
            'danger' => self::DNG_MEDIUM,
            'points' => 1
        ], [
            'id' => 32,
            'beginColor' => 0,
            'vertices' => [185, 135, 185, 120, 210, 130],
            'barycentre' => [193, 128],
            'display' => [190, 124],
            'name' => 'Secteur 32',
            'danger' => self::DNG_HARD,
            'points' => 2
        ], [
            'id' => 33,
            'beginColor' => 0,
            'vertices' => [185, 120, 210, 75, 235, 100, 215, 110, 210, 130],
            'barycentre' => [211, 107],
            'display' => [204, 100],
            'name' => 'Secteur 33',
            'danger' => self::DNG_EASY,
            'points' => 1
        ], [
            'id' => 34,
            'beginColor' => 0,
            'vertices' => [210, 130, 215, 110, 235, 100, 250, 135, 250, 145],
            'barycentre' => [232, 124],
            'display' => [226, 118],
            'name' => 'Secteur 34',
            'danger' => self::DNG_MEDIUM,
            'points' => 1
        ], [
            'id' => 35,
            'beginColor' => 0,
            'vertices' => [190, 160, 185, 135, 210, 130, 250, 145, 250, 155, 215, 150, 215, 165],
            'barycentre' => [216, 149],
            'display' => [200, 144],
            'name' => 'Secteur 35',
            'danger' => self::DNG_MEDIUM,
            'points' => 1
        ], [
            'id' => 36,
            'beginColor' => 0,
            'vertices' => [195, 180, 215, 170, 215, 165, 215, 150, 250, 155, 250, 170, 210, 195],
            'barycentre' => [221, 169],
            'display' => [225, 163],
            'name' => 'Secteur 36',
            'danger' => self::DNG_EASY,
            'points' => 1
        ], [
            'id' => 37,
            'beginColor' => 0,
            'vertices' => [195, 180, 190, 160, 215, 165, 215, 170],
            'barycentre' => [204, 169],
            'display' => [199, 166],
            'name' => 'Secteur 37',
            'danger' => self::DNG_HARD,
            'points' => 2
        ], [
            'id' => 38,
            'beginColor' => 0,
            'vertices' => [160, 205, 170, 180, 195, 180, 210, 195, 200, 210, 185, 200, 175, 200],
            'barycentre' => [185, 196],
            'display' => [185, 187],
            'name' => 'Secteur 38',
            'danger' => self::DNG_MEDIUM,
            'points' => 1
        ], [
            'id' => 39,
            'beginColor' => 0,
            'vertices' => [125, 195, 140, 180, 140, 170, 190, 160, 195, 180, 170, 180, 160, 205],
            'barycentre' => [160, 181],
            'display' => [150, 178],
            'name' => 'Secteur 39',
            'danger' => self::DNG_MEDIUM,
            'points' => 1
        ], [
            'id' => 40,
            'beginColor' => 9,
            'vertices' => [200, 250, 200, 210, 210, 235, 220, 210, 235, 205, 210, 195, 250, 170, 250, 250],
            'barycentre' => [222, 216],
            'display' => [230, 230],
            'name' => 'Secteur 40',
            'danger' => self::DNG_CASUAL,
            'points' => 1
        ], [
            'id' => 41,
            'beginColor' => 9,
            'vertices' => [200, 210, 210, 195, 235, 205, 220, 210, 210, 235],
            'barycentre' => [215, 211],
            'display' => [210, 205],
            'name' => 'Secteur 41',
            'danger' => self::DNG_CASUAL,
            'points' => 2
        ], [
            'id' => 42,
            'beginColor' => 0,
            'vertices' => [170, 250, 175, 220, 190, 225, 185, 200, 200, 210, 200, 250],
            'barycentre' => [187, 226],
            'display' => [185, 235],
            'name' => 'Secteur 42',
            'danger' => self::DNG_EASY,
            'points' => 1
        ], [
            'id' => 43,
            'beginColor' => 0,
            'vertices' => [175, 220, 175, 200, 185, 200, 190, 225],
            'barycentre' => [181, 211],
            'display' => [178, 208],
            'name' => 'Secteur 43',
            'danger' => self::DNG_HARD,
            'points' => 2
        ], [
            'id' => 44,
            'beginColor' => 0,
            'vertices' => [145, 250, 145, 235, 160, 205, 175, 200, 175, 220, 170, 250],
            'barycentre' => [162, 227],
            'display' => [157, 227],
            'name' => 'Secteur 44',
            'danger' => self::DNG_MEDIUM,
            'points' => 1
        ], [
            'id' => 45,
            'beginColor' => 0,
            'vertices' => [120, 220, 135, 215, 125, 195, 160, 205, 145, 235, 130, 230],
            'barycentre' => [136, 217],
            'display' => [140, 210],
            'name' => 'Secteur 45',
            'danger' => self::DNG_MEDIUM,
            'points' => 1
        ], [
            'id' => 46,
            'beginColor' => 0,
            'vertices' => [120, 220, 110, 205, 125, 195, 135, 215],
            'barycentre' => [123, 209],
            'display' => [120, 206],
            'name' => 'Secteur 46',
            'danger' => self::DNG_HARD,
            'points' => 2
        ], [
            'id' => 47,
            'beginColor' => 0,
            'vertices' => [65, 250, 75, 215, 110, 205, 120, 220, 130, 230, 95, 250],
            'barycentre' => [99, 228],
            'display' => [92, 228],
            'name' => 'Secteur 47',
            'danger' => self::DNG_EASY,
            'points' => 1
        ]
    ];

    public function getSectorCoord($i, $scale = 1, $xTranslate = 0)
    {
        $sector = $this->sectors[$i - 1]['vertices'];

        foreach ($sector as $k => $v) {
            $sector[$k] = (($v * $scale) + $xTranslate);
        }

        $sector = implode(', ', $sector);
        return $sector;
    }

    public function fillSectorsData()
    {
        $k = 1;

        echo '<pre>';
        foreach ($this->sectors as $key => $sector) {
            # calculate barycentre
            $strArray = $this->getSectorCoord($key + 1);
            $array = explode(', ', $strArray);

            $gx = 0;
            $gy = 0;
            $vx = 0;
            $vy = 0;
            $lenght = count($array) / 2;

            for ($j = 0; $j < count($array); $j = $j + 2) {
                $vx += $array[$j];
                $vy += $array[$j + 1];
            }

            $gx = round($vx / $lenght);
            $gy = round($vy / $lenght);

            echo '[' . "\r\n";
            echo '	\'id\' => ' . $k . ',' . "\r\n";
            echo '	\'beginColor\' => 0,' . "\r\n";
            echo '	\'vertices\' => [' . implode(', ', $sector['vertices']) . '],' . "\r\n";
            echo '	\'barycentre\' => [' . $gx . ', ' . $gy . '],' . "\r\n";
            echo '	\'display\' => [' . $gx . ', ' . $gy . '],' . "\r\n";
            echo '	\'name\' => \'Secteur ' . $k . "',\r\n";
            echo '	\'danger\' => self::DNG_CASUAL,' . "\r\n";
            echo '	\'points\' => 1' . "\r\n";
            echo '], ' . "\r\n";
        
            $k++;
        }
        echo '</pre>';
    }

    public $systems = [
        [
            'id' => 1,
            'name' => 'ruine',
            'placesPropotion' => [0, 0, 85, 10, 0, 5],
            'nbrPlaces' => [2, 6]
        ], [
            'id' => 2,
            'name' => 'nébuleuse',
            'placesPropotion' => [0, 0, 5, 90, 0, 5],
            'nbrPlaces' => [2, 8]
        ], [
            'id' => 3,
            'name' => 'géante bleue',
            'placesPropotion' => [60, 20, 2, 0, 15, 3],
            'nbrPlaces' => [8, 12]
        ], [
            'id' => 4,
            'name' => 'naine jaune',
            'placesPropotion' => [65, 15, 3, 0, 15, 2],
            'nbrPlaces' => [6, 10]
        ], [
            'id' => 5,
            'name' => 'naine rouge',
            'placesPropotion' => [75, 10, 3, 0, 10, 2],
            'nbrPlaces' => [3, 6]
        ]
    ];

    public $places = [
        [
            'id' => 1,
            'name' => 'planète tellurique',
            'resources' => 0,
            'credits' => 0,
            'history' => 0,
        ], [
            'id' => 2,
            'name' => 'planète gazeuse',
            'resources' => 38,
            'credits' => 52,
            'history' => 10,
        ], [
            'id' => 3,
            'name' => 'ruine',
            'resources' => 5,
            'credits' => 0,
            'history' => 95,
        ], [
            'id' => 4,
            'name' => 'poches de gaz',
            'resources' => 0,
            'credits' => 96,
            'history' => 4,
        ], [
            'id' => 5,
            'name' => 'ceinture d\'astéroides',
            'resources' => 98,
            'credits' => 0,
            'history' => 2,
        ], [
            'id' => 6,
            'name' => 'lieu vide',
            'resources' => 0,
            'credits' => 0,
            'history' => 0,
        ]
    ];

    # display params
    public $scale = 20;
}
