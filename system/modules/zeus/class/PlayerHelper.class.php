<?php
class PlayerHelper {
	public static function listOrbitalBases($playerId) {
		$db = DataBase::getInstance();
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

	public static function listMotherShips($playerId) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT 
				ms.rPlace, ms.name, sy.rSector
			FROM motherShip AS ms
			LEFT JOIN place AS pl
				ON pl.id = ms.rPlace
			LEFT JOIN system AS sy
				ON sy.id = pl.rSystem
			WHERE ms.rPlayer = ?');
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