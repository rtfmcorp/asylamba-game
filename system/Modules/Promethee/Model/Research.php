<?php

/**
 * Research
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Prométhée
 * @update 19.04.13
*/
namespace Asylamba\Modules\Promethee\Model;

class Research
{
    // ATTRIBUTES
    public $rPlayer;
    public $mathLevel = 0;                        //naturalTech
    public $physLevel = 0;
    public $chemLevel = 0;
    public $bioLevel = 0;    # bio == law		//lifeTech
    public $mediLevel = 0;  # medi == comm
    public $econoLevel = 0;                        //socialTech
    public $psychoLevel = 0;
    public $networkLevel = 0;                    //informaticTech
    public $algoLevel = 0;
    public $statLevel = 0;
    public $naturalTech = 0;
    public $lifeTech = 0;
    public $socialTech = 0;
    public $informaticTech = 0;
    public $naturalToPay;
    public $lifeToPay;
    public $socialToPay;
    public $informaticToPay;

    const MATH = 0;
    const PHYS = 1;
    const CHEM = 2;
    const LAW = 3;
    const COMM = 4;
    const ECONO = 5;
    const PSYCHO = 6;
    const NETWORK = 7;
    const ALGO = 8;
    const STAT = 9;

    public function getId()
    {
        return $this->rPlayer;
    }

    public function getLevel($id)
    {
        switch ($id) {
            case 0: return $this->mathLevel; break;
            case 1: return $this->physLevel; break;
            case 2: return $this->chemLevel; break;
            case 3: return $this->bioLevel; break;
            case 4: return $this->mediLevel; break;
            case 5: return $this->econoLevel; break;
            case 6: return $this->psychoLevel; break;
            case 7: return $this->networkLevel; break;
            case 8: return $this->algoLevel; break;
            case 9: return $this->statLevel; break;
            default: return false;
        }
    }
}
