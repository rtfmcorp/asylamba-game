<?php

namespace Asylamba\Modules\Promethee\Resource;

class ResearchResource
{
    /**
     * 0 = math, 1 = physique, 2 = chimie
     * 3 = biologie (droit), 4 = médecine (communication)
     * 5 = économie, 6 = psychologie
     * 7 = réseaux, 8 = algorithmique, 9 = statistiques
     **/
    public static $availableResearch = array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9);
    
    public static $research = array(
        array(
            'name' => 'Mathématiques',
            'codeName' => 'mathematics'
            ),
        array(
            'name' => 'Physique',
            'codeName' => 'physics'
            ),
        array(
            'name' => 'Chimie',
            'codeName' => 'chemistry'
            ),
        array(
            'name' => 'Droit',
            'codeName' => 'biology'
            ),
        array(
            'name' => 'Communication',
            'codeName' => 'medicine'
            ),
        array(
            'name' => 'Economie',
            'codeName' => 'economy'
            ),
        array(
            'name' => 'Psychologie',
            'codeName' => 'psychology'
            ),
        array(
            'name' => 'Réseaux',
            'codeName' => 'networks'
            ),
        array(
            'name' => 'Algorithmique',
            'codeName' => 'algorithmic'
            ),
        array(
            'name' => 'Statistiques',
            'codeName' => 'statistics'
        )
    );
}
