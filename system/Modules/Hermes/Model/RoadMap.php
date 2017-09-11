<?php

/**
 * RoadMap
 *
 * @author Jacky Casas
 * @copyright Expansion - le jeu
 *
 * @package Hermes
 * @update 21.10.13
*/

namespace Asylamba\Modules\Hermes\Model;

use Asylamba\Classes\Library\Parser;

class RoadMap
{
    // ATTRIBUTES
    public $id             = 0;
    public $rPlayer     = 0;
    public $oContent     = '';
    public $pContent     = '';
    public $statement     = 1;
    public $dCreation     = '';
    
    public $playerName     = '';
    public $playerColor     = 0;
    public $playerAvatar = '';

    // statement
    const HIDDEN = 0;
    const DISPLAYED = 1;

    public function getId()
    {
        return $this->id;
    }

    // CONSTRUCTOR
    public function __construct()
    {
    }

    public function setContent($content)
    {
        $this->oContent = $content;
    }
    
    public function setParsedContent($content)
    {
        $this->pContent = $content;
    }

    public function hide()
    {
        $this->statement = 0;
    }
    public function show()
    {
        $this->statement = 1;
    }
}
