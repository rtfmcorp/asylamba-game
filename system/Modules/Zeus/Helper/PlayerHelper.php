<?php

namespace Asylamba\Modules\Zeus\Helper;

use Asylamba\Classes\Database\Database;

class PlayerHelper {
	public static function listOrbitalBases($playerId) {
		$db = Database::getInstance();
		$qr = $db->prepare('SELECT 
				ob.rPlace, ob.name, sy.rSector
			FROM orbitalBase AS ob
			LEFT JOIN place AS pl
				ON pl.id = ob.rPlace
			LEFT JOIN system AS sy
				ON sy.id = pl.rSystem
			WHERE ob.rPlayer = ?');
		$qr->execute(array($playerId));
		$aw = $qr->fetchAll();
		if (empty($aw)) {
			return FALSE;
		} else {
			foreach ($aw as $k => $v) { $return[] = array('id' => $v['rPlace'], 'name' => $v['name'], 'sector' => $v['rSector']); }
			return $return;
		}
	}
}