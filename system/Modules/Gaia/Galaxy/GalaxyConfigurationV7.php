<?php

namespace Asylamba\Modules\Gaia\Galaxy;

class GalaxyConfigurationV7 extends GalaxyConfiguration {
    public $galaxy = [
        'size' => 250,
        'diag' => 177,
        'mask' => 15,
        'systemProportion'        => [3, 8, 9, 25, 55],
        'systemPosition'        => NULL,
        'lineSystemPosition' => [
            #    [[pA], [pB], EPAISSEUR, INTENSITE],
            [[15, 45], [15, 250], 40, 8],
            [[230, 215], [230, 0], 40, 8],
            [[215, 15], [0, 15], 40, 8],
            [[35, 235], [250, 235], 40, 8],
            [[50, 125], [200, 125], 12, 6],
            [[125, 50], [125, 200], 12, 6],
            [[50, 80], [80, 50], 10, 8], # soit les 4 ici en dessous pour les losanges
            [[200, 80], [170, 50], 10, 8],
            [[50, 170], [80, 200], 10, 8],
            [[200, 170], [170, 200], 10, 8]
        ],
        'circleSystemPosition' => [
        #    [[X1], RAYON, EPAISSEUR, INTENSITE],
            [[ 125, 125], 25, 15, 8],
            //[[ 125, 125], 82, 15, 8] #soit celui ci pour les losanges
        ],
        'population' => [700, 25000],
    ];

        public $sectors = [
                /*[
                        'id' => 1,
                        'beginColor' => 0,
                        'vertices' => [0, 250, 250, 250, 250, 0, 0, 0],
                        'barycentre' => [23, 232],
                        'display' => [23, 232],
                        'name' => 'Secteur 1',
                        'danger' => GalaxyConfiguration::DNG_CASUAL,
                        'points' => 1
                ]*/[
        'id' => 1,
        'beginColor' => 0,
        'vertices' => [0, 50, 0, 70, 30, 80, 10, 60, 20, 50, 10, 40],
        'barycentre' => [12, 58],
        'display' => [12, 58],
        'name' => 'Secteur 1',
        'danger' => 2,
        'points' => 1
], 
[
        'id' => 2,
        'beginColor' => 0,
        'vertices' => [0, 70, 30, 80, 30, 90, 0, 90],
        'barycentre' => [15, 83],
        'display' => [15, 83],
        'name' => 'Secteur 2',
        'danger' => 2,
        'points' => 1
], 
[
        'id' => 3,
        'beginColor' => 4,
        'vertices' => [0, 90, 30, 90, 40, 90, 40, 110, 40, 120, 20, 130, 0, 110],
        'barycentre' => [24, 106],
        'display' => [24, 106],
        'name' => 'Secteur 3',
        'danger' => 1,
        'points' => 1
], 
[
        'id' => 4,
        'beginColor' => 4,
        'vertices' => [0, 110, 20, 130, 40, 160, 0, 160],
        'barycentre' => [15, 140],
        'display' => [15, 140],
        'name' => 'Secteur 4',
        'danger' => 1,
        'points' => 1
], 
[
        'id' => 5,
        'beginColor' => 0,
        'vertices' => [20, 130, 40, 120, 40, 140, 40, 160],
        'barycentre' => [35, 138],
        'display' => [35, 138],
        'name' => 'Secteur 5',
        'danger' => 1,
        'points' => 1
], 
[
        'id' => 6,
        'beginColor' => 0,
        'vertices' => [0,160,40,160,40,180,20,200,0,190],
        'barycentre' => [20, 178],
        'display' => [20, 178],
        'name' => 'Secteur 6',
        'danger' => 2,
        'points' => 1
], 
[
        'id' => 7,
        'beginColor' => 0,
        'vertices' => [0, 190, 20, 200, 20, 210, 10, 220, 0, 210],
        'barycentre' => [10, 206],
        'display' => [10, 206],
        'name' => 'Secteur 7',
        'danger' => 2,
        'points' => 1
], 
[
        'id' => 8,
        'beginColor' => 0,
        'vertices' => [0, 210, 10, 220, 20, 230, 0, 230],
        'barycentre' => [8, 223],
        'display' => [8, 223],
        'name' => 'Secteur 8',
        'danger' => 3,
        'points' => 1
], 
[
        'id' => 9,
        'beginColor' => 0,
        'vertices' => [0, 230, 20, 230, 20, 250, 0, 250],
        'barycentre' => [10, 240],
        'display' => [10, 240],
        'name' => 'Secteur 9',
        'danger' => 3,
        'points' => 4
], 
[
        'id' => 10,
        'beginColor' => 0,
        'vertices' => [50, 250, 40, 240, 50, 230, 60, 230, 70, 240, 60, 250],
        'barycentre' => [55, 240],
        'display' => [55, 240],
        'name' => 'Secteur 10',
        'danger' => 2,
        'points' => 1
], 
[
        'id' => 11,
        'beginColor' => 0,
        'vertices' => [60, 250, 70, 240, 90, 220, 90, 250],
        'barycentre' => [78, 240],
        'display' => [78, 240],
        'name' => 'Secteur 11',
        'danger' => 2,
        'points' => 1
], 
[
        'id' => 12,
        'beginColor' => 5,
        'vertices' => [90, 250, 90, 220, 90, 210, 100, 230, 120, 210, 130, 250],
        'barycentre' => [103, 228],
        'display' => [103, 228],
        'name' => 'Secteur 12',
        'danger' => 1,
        'points' => 1
], 
[
        'id' => 13,
        'beginColor' => 0,
        'vertices' => [90, 210, 110, 210, 130, 210, 120, 210, 100, 230],
        'barycentre' => [110, 214],
        'display' => [110, 214],
        'name' => 'Secteur 13',
        'danger' => 1,
        'points' => 1
], 
[
        'id' => 14,
        'beginColor' => 5,
        'vertices' => [130, 210, 120, 210, 130, 250, 160, 250, 160, 210, 140, 210],
        'barycentre' => [140, 223],
        'display' => [140, 223],
        'name' => 'Secteur 14',
        'danger' => 1,
        'points' => 1
], 
[
        'id' => 15,
        'beginColor' => 0,
        'vertices' => [160, 210, 160, 250, 190, 250, 180, 230],
        'barycentre' => [173, 235],
        'display' => [173, 235],
        'name' => 'Secteur 15',
        'danger' => 2,
        'points' => 1
], 
[
        'id' => 16,
        'beginColor' => 0,
        'vertices' => [190, 250, 180, 230, 190, 220, 220, 240, 210, 250],
        'barycentre' => [198, 238],
        'display' => [198, 238],
        'name' => 'Secteur 16',
        'danger' => 2,
        'points' => 1
], 
[
        'id' => 17,
        'beginColor' => 0,
        'vertices' => [210, 250, 220, 240, 230, 230, 230, 250],
        'barycentre' => [223, 243],
        'display' => [223, 243],
        'name' => 'Secteur 17',
        'danger' => 3,
        'points' => 1
], 
[
        'id' => 18,
        'beginColor' => 0,
        'vertices' => [230, 250, 230, 230, 250, 230, 250, 250],
        'barycentre' => [240, 240],
        'display' => [240, 240],
        'name' => 'Secteur 18',
        'danger' => 3,
        'points' => 4
], 
[
        'id' => 19,
        'beginColor' => 0,
        'vertices' => [250, 200, 240, 210, 230, 200, 240, 180, 250, 170],
        'barycentre' => [242, 192],
        'display' => [242, 192],
        'name' => 'Secteur 19',
        'danger' => 2,
        'points' => 1
], 
[
        'id' => 20,
        'beginColor' => 0,
        'vertices' => [220, 160, 240, 180, 250, 170, 250, 160],
        'barycentre' => [240, 168],
        'display' => [240, 168],
        'name' => 'Secteur 20',
        'danger' => 2,
        'points' => 1
], 
[
        'id' => 21,
        'beginColor' => 1,
        'vertices' => [250, 160, 220, 160, 210, 160, 230, 120, 250, 120],
        'barycentre' => [232, 144],
        'display' => [232, 144],
        'name' => 'Secteur 21',
        'danger' => 1,
        'points' => 1
], 
[
        'id' => 22,
        'beginColor' => 0,
        'vertices' => [210, 160, 210, 140, 210, 110, 230, 120],
        'barycentre' => [215, 133],
        'display' => [215, 133],
        'name' => 'Secteur 22',
        'danger' => 1,
        'points' => 1
], 
[
        'id' => 23,
        'beginColor' => 1,
        'vertices' => [250, 120, 230, 120, 210, 110, 210, 90, 250, 90],
        'barycentre' => [230, 106],
        'display' => [230, 106],
        'name' => 'Secteur 23',
        'danger' => 1,
        'points' => 1
], 
[
        'id' => 24,
        'beginColor' => 0,
        'vertices' => [250, 90, 210, 90, 230, 70, 250, 60],
        'barycentre' => [235, 78],
        'display' => [235, 78],
        'name' => 'Secteur 24',
        'danger' => 2,
        'points' => 1
], 
[
        'id' => 25,
        'beginColor' => 0,
        'vertices' => [250, 60, 230, 70, 220, 50, 240, 40, 250, 50],
        'barycentre' => [238, 54],
        'display' => [238, 54],
        'name' => 'Secteur 25',
        'danger' => 2,
        'points' => 1
], 
[
        'id' => 26,
        'beginColor' => 0,
        'vertices' => [250, 50, 240, 40, 230, 20, 250, 20],
        'barycentre' => [243, 33],
        'display' => [243, 33],
        'name' => 'Secteur 26',
        'danger' => 3,
        'points' => 1
], 
[
        'id' => 27,
        'beginColor' => 0,
        'vertices' => [250, 20, 230, 20, 230, 0, 250, 0],
        'barycentre' => [240, 10],
        'display' => [240, 10],
        'name' => 'Secteur 27',
        'danger' => 3,
        'points' => 4
], 
[
        'id' => 28,
        'beginColor' => 0,
        'vertices' => [200, 0, 210, 10, 200, 20, 180, 10, 180, 0],
        'barycentre' => [194, 8],
        'display' => [194, 8],
        'name' => 'Secteur 28',
        'danger' => 2,
        'points' => 1
], 
[
        'id' => 29,
        'beginColor' => 0,
        'vertices' => [180, 0, 180, 10, 160, 40, 160, 0],
        'barycentre' => [170, 13],
        'display' => [170, 13],
        'name' => 'Secteur 29',
        'danger' => 2,
        'points' => 1
], 
[
        'id' => 30,
        'beginColor' => 3,
        'vertices' => [160, 0, 160, 40, 130, 30, 120, 20, 130, 0],
        'barycentre' => [140, 18],
        'display' => [140, 18],
        'name' => 'Secteur 30',
        'danger' => 1,
        'points' => 1
], 
[
        'id' => 31,
        'beginColor' => 0,
        'vertices' => [160, 40, 140, 40, 110, 40, 90, 40, 120, 20, 130, 30],
        'barycentre' => [125, 35],
        'display' => [125, 35],
        'name' => 'Secteur 31',
        'danger' => 1,
        'points' => 1
], 
[
        'id' => 32,
        'beginColor' => 3,
        'vertices' => [130, 0, 120, 20, 90, 40, 90, 0],
        'barycentre' => [108, 15],
        'display' => [108, 15],
        'name' => 'Secteur 32',
        'danger' => 1,
        'points' => 1
], 
[
        'id' => 33,
        'beginColor' => 0,
        'vertices' => [90, 0, 90, 40, 70, 30, 60, 0],
        'barycentre' => [78, 18],
        'display' => [78, 18],
        'name' => 'Secteur 33',
        'danger' => 2,
        'points' => 1
], 
[
        'id' => 34,
        'beginColor' => 0,
        'vertices' => [60, 0, 70, 30, 50, 30, 40, 10, 50, 0],
        'barycentre' => [54, 14],
        'display' => [54, 14],
        'name' => 'Secteur 34',
        'danger' => 2,
        'points' => 1
], 
[
        'id' => 35,
        'beginColor' => 0,
        'vertices' => [50, 0, 40, 10, 20, 20, 20, 0],
        'barycentre' => [33, 8],
        'display' => [33, 8],
        'name' => 'Secteur 35',
        'danger' => 3,
        'points' => 1
], 
[
        'id' => 36,
        'beginColor' => 0,
        'vertices' => [20, 0, 20, 20, 0, 20, 0, 0],
        'barycentre' => [10, 10],
        'display' => [10, 10],
        'name' => 'Secteur 36',
        'danger' => 3,
        'points' => 4
], 
[
        'id' => 37,
        'beginColor' => 0,
        'vertices' => [80, 50, 60, 60, 70, 60, 70, 70],
        'barycentre' => [70, 60],
        'display' => [70, 60],
        'name' => 'Secteur 37',
        'danger' => 2,
        'points' => 1
], 
[
        'id' => 38,
        'beginColor' => 0,
        'vertices' => [60, 60, 70, 60, 70, 70, 60, 70],
        'barycentre' => [65, 65],
        'display' => [65, 65],
        'name' => 'Secteur 38',
        'danger' => 3,
        'points' => 5
], 
[
        'id' => 39,
        'beginColor' => 0,
        'vertices' => [60, 60, 50, 80, 70, 70, 60, 70],
        'barycentre' => [60, 70],
        'display' => [60, 70],
        'name' => 'Secteur 39',
        'danger' => 2,
        'points' => 1
], 
[
        'id' => 40,
        'beginColor' => 0,
        'vertices' => [40, 110, 40, 120, 40, 140, 60, 130, 60, 120],
        'barycentre' => [48, 124],
        'display' => [48, 124],
        'name' => 'Secteur 40',
        'danger' => 2,
        'points' => 1
], 
[
        'id' => 41,
        'beginColor' => 0,
        'vertices' => [60, 120, 60, 130, 90, 130, 90, 120],
        'barycentre' => [75, 125],
        'display' => [75, 125],
        'name' => 'Secteur 41',
        'danger' => 3,
        'points' => 1
], 
[
        'id' => 42,
        'beginColor' => 0,
        'vertices' => [50, 170, 60, 190, 60, 180, 70, 180],
        'barycentre' => [60, 180],
        'display' => [60, 180],
        'name' => 'Secteur 42',
        'danger' => 2,
        'points' => 1
], 
[
        'id' => 43,
        'beginColor' => 0,
        'vertices' => [60, 180, 60, 190, 70, 190, 70, 180],
        'barycentre' => [65, 185],
        'display' => [65, 185],
        'name' => 'Secteur 43',
        'danger' => 3,
        'points' => 2
], 
[
        'id' => 44,
        'beginColor' => 0,
        'vertices' => [60, 190, 80, 200, 70, 180, 70, 190],
        'barycentre' => [70, 190],
        'display' => [70, 190],
        'name' => 'Secteur 44',
        'danger' => 2,
        'points' => 1
], 
[
        'id' => 45,
        'beginColor' => 0,
        'vertices' => [110, 210, 130, 210, 140, 210, 130, 190, 120, 190],
        'barycentre' => [126, 202],
        'display' => [126, 202],
        'name' => 'Secteur 45',
        'danger' => 2,
        'points' => 1
], 
[
        'id' => 46,
        'beginColor' => 0,
        'vertices' => [120, 160, 120, 190, 130, 190, 130, 160],
        'barycentre' => [125, 175],
        'display' => [125, 175],
        'name' => 'Secteur 46',
        'danger' => 3,
        'points' => 1
], 
[
        'id' => 47,
        'beginColor' => 0,
        'vertices' => [170, 200, 190, 190, 180, 190, 180, 180],
        'barycentre' => [180, 190],
        'display' => [180, 190],
        'name' => 'Secteur 47',
        'danger' => 2,
        'points' => 1
], 
[
        'id' => 48,
        'beginColor' => 0,
        'vertices' => [180, 190, 190, 190, 190, 180, 180, 180],
        'barycentre' => [185, 185],
        'display' => [185, 185],
        'name' => 'Secteur 48',
        'danger' => 3,
        'points' => 5
], 
[
        'id' => 49,
        'beginColor' => 0,
        'vertices' => [190, 190, 200, 170, 180, 180, 190, 180],
        'barycentre' => [190, 180],
        'display' => [190, 180],
        'name' => 'Secteur 49',
        'danger' => 3,
        'points' => 1
], 
[
        'id' => 50,
        'beginColor' => 0,
        'vertices' => [210, 140, 210, 110, 190, 120, 190, 130],
        'barycentre' => [200, 125],
        'display' => [200, 125],
        'name' => 'Secteur 50',
        'danger' => 2,
        'points' => 1
], 
[
        'id' => 51,
        'beginColor' => 0,
        'vertices' => [160, 120, 160, 130, 190, 130, 190, 120],
        'barycentre' => [175, 125],
        'display' => [175, 125],
        'name' => 'Secteur 51',
        'danger' => 3,
        'points' => 1
], 
[
        'id' => 52,
        'beginColor' => 0,
        'vertices' => [180, 70, 200, 80, 190, 60, 190, 70],
        'barycentre' => [190, 70],
        'display' => [190, 70],
        'name' => 'Secteur 52',
        'danger' => 2,
        'points' => 1
], 
[
        'id' => 53,
        'beginColor' => 0,
        'vertices' => [180, 60, 180, 70, 190, 70, 190, 60],
        'barycentre' => [185, 65],
        'display' => [185, 65],
        'name' => 'Secteur 53',
        'danger' => 3,
        'points' => 2
], 
[
        'id' => 54,
        'beginColor' => 0,
        'vertices' => [170, 50, 180, 70, 180, 60, 190, 60],
        'barycentre' => [180, 60],
        'display' => [180, 60],
        'name' => 'Secteur 54',
        'danger' => 2,
        'points' => 1
], 
[
        'id' => 55,
        'beginColor' => 0,
        'vertices' => [110, 40, 120, 60, 130, 60, 140, 40],
        'barycentre' => [125, 50],
        'display' => [125, 50],
        'name' => 'Secteur 55',
        'danger' => 2,
        'points' => 1
], 
[
        'id' => 56,
        'beginColor' => 0,
        'vertices' => [120, 60, 120, 90, 130, 90, 130, 60],
        'barycentre' => [125, 75],
        'display' => [125, 75],
        'name' => 'Secteur 56',
        'danger' => 3,
        'points' => 1
], 
[
        'id' => 57,
        'beginColor' => 0,
        'vertices' => [120, 90, 100, 100, 120, 100, 140, 100, 150, 100, 130, 90],
        'barycentre' => [127, 97],
        'display' => [127, 97],
        'name' => 'Secteur 57',
        'danger' => 3,
        'points' => 1
], 
[
        'id' => 58,
        'beginColor' => 0,
        'vertices' => [100, 100, 100, 120, 120, 100],
        'barycentre' => [107, 107],
        'display' => [107, 107],
        'name' => 'Secteur 58',
        'danger' => 4,
        'points' => 2
], 
[
        'id' => 59,
        'beginColor' => 0,
        'vertices' => [100, 100, 90, 120, 90, 130, 100, 150, 100, 140, 100, 120],
        'barycentre' => [97, 127],
        'display' => [97, 127],
        'name' => 'Secteur 59',
        'danger' => 3,
        'points' => 1
], 
[
        'id' => 60,
        'beginColor' => 0,
        'vertices' => [100, 120, 100, 140, 110, 130],
        'barycentre' => [103, 130],
        'display' => [103, 130],
        'name' => 'Secteur 60',
        'danger' => 4,
        'points' => 2
], 
[
        'id' => 61,
        'beginColor' => 0,
        'vertices' => [100, 140, 100, 150, 110, 150, 120, 140, 110, 130],
        'barycentre' => [108, 142],
        'display' => [108, 142],
        'name' => 'Secteur 61',
        'danger' => 5,
        'points' => 3
], 
[
        'id' => 62,
        'beginColor' => 0,
        'vertices' => [110, 150, 130, 150, 120, 140],
        'barycentre' => [120, 147],
        'display' => [120, 147],
        'name' => 'Secteur 62',
        'danger' => 4,
        'points' => 2
], 
[
        'id' => 63,
        'beginColor' => 0,
        'vertices' => [100, 150, 120, 160, 130, 160, 150, 150, 130, 150, 110, 150],
        'barycentre' => [123, 153],
        'display' => [123, 153],
        'name' => 'Secteur 63',
        'danger' => 3,
        'points' => 1
], 
[
        'id' => 64,
        'beginColor' => 0,
        'vertices' => [130, 150, 150, 150, 150, 130],
        'barycentre' => [143, 143],
        'display' => [143, 143],
        'name' => 'Secteur 64',
        'danger' => 4,
        'points' => 2
], 
[
        'id' => 65,
        'beginColor' => 0,
        'vertices' => [150, 150, 160, 130, 160, 120, 150, 100, 150, 110, 150, 130],
        'barycentre' => [153, 123],
        'display' => [153, 123],
        'name' => 'Secteur 65',
        'danger' => 4,
        'points' => 2
], 
[
        'id' => 66,
        'beginColor' => 0,
        'vertices' => [150, 110, 140, 120, 150, 130],
        'barycentre' => [147, 120],
        'display' => [147, 120],
        'name' => 'Secteur 66',
        'danger' => 4,
        'points' => 2
], 
[
        'id' => 67,
        'beginColor' => 0,
        'vertices' => [150, 100, 140, 100, 130, 110, 140, 120, 150, 110],
        'barycentre' => [142, 108],
        'display' => [142, 108],
        'name' => 'Secteur 67',
        'danger' => 5,
        'points' => 3
], 
[
        'id' => 68,
        'beginColor' => 0,
        'vertices' => [140, 100, 120, 100, 130, 110],
        'barycentre' => [130, 103],
        'display' => [130, 103],
        'name' => 'Secteur 68',
        'danger' => 4,
        'points' => 2
], 
[
        'id' => 69,
        'beginColor' => 0,
        'vertices' => [120, 100, 100, 120, 110, 130, 130, 110],
        'barycentre' => [115, 115],
        'display' => [115, 115],
        'name' => 'Secteur 69',
        'danger' => 5,
        'points' => 3
], 
[
        'id' => 70,
        'beginColor' => 0,
        'vertices' => [110, 130, 120, 140, 140, 120, 130, 110],
        'barycentre' => [125, 125],
        'display' => [125, 125],
        'name' => 'Secteur 70',
        'danger' => 5,
        'points' => 10
], 
[
        'id' => 71,
        'beginColor' => 0,
        'vertices' => [140, 120, 120, 140, 130, 150, 150, 130],
        'barycentre' => [135, 135],
        'display' => [135, 135],
        'name' => 'Secteur 71',
        'danger' => 5,
        'points' => 3
], 
            ];
                
        public function getSectorCoord($i, $scale = 1, $xTranslate = 0) {
                $sector = $this->sectors[$i - 1]['vertices'];
                foreach ($sector as $k => $v) {
                        $sector[$k] = (($v * $scale) + $xTranslate);
                }
                $sector = implode(', ', $sector);
                return $sector;
        }

        public function fillSectorsData() {
                $k = 1;
                echo '<pre>';
                foreach ($this->sectors as $key => $sector) {
                        # calculate barycentre
                        $strArray = self::getSectorCoord($key + 1);
                        $array = explode(', ', $strArray);
                        $gx = 0; $gy = 0;
                        $vx = 0; $vy = 0;
                        $lenght = count($array) / 2;
                        for ($j = 0; $j < count($array); $j = $j + 2) {
                                $vx += $array[$j];
                                $vy += $array[$j + 1];
                        }
                        $gx = round($vx / $lenght);
                        $gy = round($vy / $lenght);
                        echo '[' . "\r\n";
                        echo '        \'id\' => ' . $k . ',' . "\r\n";
                        echo '        \'beginColor\' => ' . $sector['beginColor'] . ',' . "\r\n";
                        echo '        \'vertices\' => [' . implode(', ', $sector['vertices']) . '],' . "\r\n";
                        echo '        \'barycentre\' => [' . $gx . ', ' . $gy . '],' . "\r\n";
                        echo '        \'display\' => [' . $gx . ', ' . $gy . '],' . "\r\n";
                        echo '        \'name\' => \'Secteur ' . $k . "',\r\n";
                        echo '        \'danger\' => ' . $sector['danger'] . ',' . "\r\n";
                        echo '        \'points\' => ' . $sector['points'] . '' . "\r\n";
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
