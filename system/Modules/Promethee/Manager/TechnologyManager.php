<?php

namespace Asylamba\Modules\Promethee\Manager;

use Asylamba\Modules\Promethee\Model\Technology;

use Asylamba\Classes\Database\Database;
use Asylamba\Modules\Zeus\Manager\PlayerBonusManager;
use Asylamba\Modules\Promethee\Helper\TechnologyHelper;

use Asylamba\Modules\Zeus\Model\Player;

class TechnologyManager
{
    /** @var Database **/
    protected $database;
    /** @var PlayerBonusManager **/
    protected $playerBonusManager;
    /** @var TechnologyHelper **/
    protected $technologyHelper;
    
    /**
     * @param Database $database
     * @param PlayerBonusManager $playerBonusManager
     * @param TechnologyHelper $technologyHelper
     */
    public function __construct(Database $database, PlayerBonusManager $playerBonusManager, TechnologyHelper $technologyHelper)
    {
        $this->database = $database;
        $this->playerBonusManager = $playerBonusManager;
        $this->technologyHelper = $technologyHelper;
    }
    
    /**
     * @param int $playerId
     * @return Technology
     */
    public function getPlayerTechnology($playerId)
    {
        $technology = new Technology();
        $technology->rPlayer = $playerId;
        
        $statement = $this->database->prepare('SELECT * FROM technology WHERE rPlayer = :player_id');
        $statement->execute([
            'player_id' => $playerId
        ]);
        while ($row = $statement->fetch()) {
            $technology->setTechnology($row['technology'], $row['level'], true);
        }
        return $technology;
    }
    
    /**
     * @param Technology $technology
     * @param int $id
     * @param string $value
     * @param Player $player
     * @param boolean $load
     * @return boolean
     */
    public function affectTechnology(Technology $technology, $id, $value, Player $player, $load = false)
    { // ajouter une entrée bdd ou modifier ligne !!!
        if ($technology->setTechnology($id, $value) === false) {
            return false;
        }
        if ($load == true) {
            return true;
        } else {
            if ($value < 1) {
                $this->deleteByRPlayer($technology->rPlayer, $id);
            } else {
                if ($value == 1) {
                    $this->addTech($technology->rPlayer, $id, $value);
                } else {
                    $this->updateTech($technology->rPlayer, $id, $value);
                }
                if (!$this->technologyHelper->isAnUnblockingTechnology($id)) {
                    $bonus = $this->playerBonusManager->getBonusByPlayer($player);
                    $this->playerBonusManager->load($bonus);
                    $this->playerBonusManager->updateTechnoBonus($bonus, $id, $value);
                }
            }
            return true;
        }
    }

    public function addTech($playerId, $technology, $level)
    {
        $statement = $this->database->prepare('INSERT INTO technology(rPlayer, technology, level) VALUES(:player_id, :technology, :level)');
        return $statement->execute([
            'player_id' => $playerId,
            'technology' => $technology,
            'level' => $level
        ]);
    }

    public function updateTech($playerId, $technology, $level)
    {
        $statement = $this->database->prepare('UPDATE technology SET level = :level WHERE rPlayer = :player_id AND technology = :technology');
        return $statement->execute([
            'level' => $level,
            'player_id' => $playerId,
            'technology' => $technology
        ]);
    }

    /**
     * @param Technology $technology
     * @param type $technologyId
     */
    public function delete(Technology $technology, $technologyId)
    {
        $technology->setTechnology($technologyId, 0);
    }

    /**
     * @param int $playerId
     * @param int $technology
     * @return boolean
     */
    public function deleteByRPlayer($playerId, $technology)
    {
        $statement = $this->database->prepare('DELETE FROM technology WHERE rPlayer = :player_id and technology = :technology');
        return $statement->execute([
            'player_id' => $playerId,
            'technology' => $technology
        ]);
    }
}
