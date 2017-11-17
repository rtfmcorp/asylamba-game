<?php

/**
 * ShipQueue
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Athena
 * @update 10.02.14
*/
namespace Asylamba\Modules\Athena\Model;

class ShipQueue
{
    // ATTRIBUTES
    public $id;
    public $rOrbitalBase;
    public $dockType = 0;
    public $shipNumber    = 0;
    public $quantity = 1;
    public $dStart;
    public $dEnd;

    public function getId()
    {
        return $this->id;
    }
}
