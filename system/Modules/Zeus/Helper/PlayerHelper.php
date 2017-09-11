<?php

namespace Asylamba\Modules\Zeus\Helper;

use Asylamba\Classes\Database\Database;

class PlayerHelper
{
    /** @var Database **/
    protected $database;
    
    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
    }
    
    /**
     * @param int $playerId
     * @return boolean
     */
    public static function listOrbitalBases($playerId)
    {
        $qr = $this->database->prepare('SELECT 
				ob.rPlace, ob.name, sy.rSector
			FROM orbitalBase AS ob
			LEFT JOIN place AS pl
				ON pl.id = ob.rPlace
			LEFT JOIN system AS sy
				ON sy.id = pl.rSystem
			WHERE ob.rPlayer = ?');
        $qr->execute(array($playerId));
        /** @TODO Fetch correctly that monstruosity **/
        $aw = $qr->fetchAll();
        if (empty($aw)) {
            return false;
        } else {
            foreach ($aw as $k => $v) {
                $return[] = array('id' => $v['rPlace'], 'name' => $v['name'], 'sector' => $v['rSector']);
            }
            return $return;
        }
    }
}
