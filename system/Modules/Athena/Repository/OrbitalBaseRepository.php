<?php

namespace Asylamba\Modules\Athena\Repository;

use Asylamba\Classes\Entity\AbstractRepository;

use Asylamba\Classes\Library\Utils;

use Asylamba\Modules\Athena\Model\OrbitalBase;

class OrbitalBaseRepository extends AbstractRepository {
	public function select($clause = '', $params = [])
	{
		$statement = $this->connection->prepare(
			'SELECT ob.*,
			p.position AS position, p.rSystem AS system,
			s.xPosition AS xSystem, s.yPosition AS ySystem, s.rSector AS sector,
			se.rColor AS sectorColor, se.tax AS tax,
			p.population AS planetPopulation, p.coefResources AS planetResources, p.coefHistory AS planetHistory,
			
			(SELECT MAX(bq.dEnd) FROM orbitalBaseBuildingQueue AS bq WHERE bq.rOrbitalBase = ob.rPlace) AS termDateGenerator,
			(SELECT MAX(sq1.dEnd) FROM orbitalBaseShipQueue AS sq1 WHERE sq1.rOrbitalBase = ob.rPlace AND sq1.dockType = 1) AS termDateDock1,
			(SELECT MAX(sq2.dEnd) FROM orbitalBaseShipQueue AS sq2 WHERE sq2.rOrbitalBase = ob.rPlace AND sq2.dockType = 2) AS termDateDock2,
			(SELECT MAX(sq3.dEnd) FROM orbitalBaseShipQueue AS sq3 WHERE sq3.rOrbitalBase = ob.rPlace AND sq3.dockType = 3) AS termDateDock3,
			(SELECT COUNT(cr.id) FROM commercialRoute AS cr WHERE (cr.rOrbitalBase = ob.rPlace OR cr.rOrbitalBaseLinked = ob.rPlace) AND cr.statement = 1) AS routesNumber
			
			FROM orbitalBase AS ob
			LEFT JOIN place AS p ON ob.rPlace = p.id
			LEFT JOIN system AS s ON p.rSystem = s.id
			LEFT JOIN sector AS se ON s.rSector = se.id ' .
			$clause
		);
		$statement->execute($params);
		return $statement;
	}
	
	/**
	 * @param int $id
	 * @return OrbitalBase
	 */
	public function get($id)
	{
		if (($ob = $this->unitOfWork->getObject(OrbitalBase::class, $id)) !== null) {
			return $ob;
		}
		$statement = $this->select('WHERE ob.rPlace = :id', ['id' => $id]);
		if (($row = $statement->fetch()) === false) {
			return null;
		}
		$orbitalBase = $this->format($row);
		$this->unitOfWork->addObject($orbitalBase);
		return $orbitalBase;
	}
	
	/**
	 * @param int $playerId
	 * @return array
	 */
	public function getPlayerBases($playerId)
	{
		$statement = $this->select('WHERE ob.rPlayer = :player_id', ['player_id' => $playerId]);
		$data = [];
		while ($row = $statement->fetch()) {
			if (($ob = $this->unitOfWork->getObject(OrbitalBase::class, $row['rPlace'])) !== null) {
				$data[] = $ob;
				continue;
			}
			$orbitalBase = $this->format($row);
			$this->unitOfWork->addObject($orbitalBase);
			$data[] = $orbitalBase;
		}
		return $data;
	}
	
	/**
	 * @param int $baseId
	 * @param int $playerId
	 * @return OrbitalBase
	 */
	public function getPlayerBase($baseId, $playerId)
	{
		$statement = $this->select('WHERE ob.rPlace = :id AND ob.rPlayer = :player_id', ['id' => $baseId, 'player_id' => $playerId]);
		if (($row = $statement->fetch()) === false) {
			return null;
		}
		if (($ob = $this->unitOfWork->getObject(OrbitalBase::class, $row['rPlace'])) !== null) {
			return $ob;
		}
		$orbitalBase = $this->format($row);
		$this->unitOfWork->addObject($orbitalBase);
		return $orbitalBase;
	}
	
	public function insert($orbitalBase)
	{
		$statement = $this->connection->prepare(
			'INSERT INTO orbitalBase(rPlace, rPlayer, name, typeOfBase, levelGenerator,
				levelRefinery, levelDock1, levelDock2, levelDock3, levelTechnosphere, levelCommercialPlateforme,
				levelStorage, levelRecycling, levelSpatioport, points, iSchool, iAntiSpy, antiSpyAverage, 
				pegaseStorage, satyreStorage, sireneStorage, dryadeStorage, chimereStorage, meduseStorage,
				griffonStorage, cyclopeStorage, minotaureStorage, hydreStorage, cerbereStorage, phenixStorage,
				resourcesStorage, uOrbitalBase, dCreation)
			VALUES(:id, :player_id, :name, :type, :generator_level, :refinery_level, :dock1_level, :dock2_level, :dock3_level,
				:technosphere_level, :commercial_platform_level, :storage_level, :recycling_level, :spatioport_level, :points, :school_investments,  
				:anti_spy_investments, :anti_spy_average, :pegase_storage, :satyre_storage, :sirene_storage, :dryade_storage, 
				:chimere_storage, :meduse_storage, :griffon_storage, :cyclope_storage, :minotaure_storage, :hydre_storage, :cerbere_storage,
				:phenix_storage, :resources, :u_orbital_base, :created_at)'
		);
		$statement->execute([
			'id' => $orbitalBase->getRPlace(),
			'player_id' => $orbitalBase->getRPlayer(),
			'name' => $orbitalBase->getName(),
			'type' => $orbitalBase->typeOfBase,
			'generator_level' => $orbitalBase->getLevelGenerator(),
			'refinery_level' => $orbitalBase->getLevelRefinery(),
			'dock1_level' => $orbitalBase->getLevelDock1(),
			'dock2_level' => $orbitalBase->getLevelDock2(),
			'dock3_level' => $orbitalBase->getLevelDock3(),
			'technosphere_level' => $orbitalBase->getLevelTechnosphere(),
			'commercial_platform_level' => $orbitalBase->getLevelCommercialPlateforme(),
			'storage_level' => $orbitalBase->getLevelStorage(),
			'recycling_level' => $orbitalBase->getLevelRecycling(),
			'spatioport_level' => $orbitalBase->getLevelSpatioport(),
			'points' => $orbitalBase->getPoints(),
			'school_investments' => $orbitalBase->getISchool(),
			'anti_spy_investments' => $orbitalBase->getIAntiSpy(),
			'anti_spy_average' => $orbitalBase->getAntiSpyAverage(),
			'pegase_storage' => $orbitalBase->getShipStorage(0),
			'satyre_storage' => $orbitalBase->getShipStorage(1),
			'sirene_storage' => $orbitalBase->getShipStorage(2),
			'dryade_storage' => $orbitalBase->getShipStorage(3),
			'chimere_storage' => $orbitalBase->getShipStorage(4),
			'meduse_storage' => $orbitalBase->getShipStorage(5),
			'griffon_storage' => $orbitalBase->getShipStorage(6),
			'cyclope_storage' => $orbitalBase->getShipStorage(7),
			'minotaure_storage' => $orbitalBase->getShipStorage(8),
			'hydre_storage' => $orbitalBase->getShipStorage(9),
			'cerbere_storage' => $orbitalBase->getShipStorage(10),
			'phenix_storage' => $orbitalBase->getShipStorage(11),
			'resources' => $orbitalBase->getResourcesStorage(),
			'u_orbital_base' => $orbitalBase->uOrbitalBase,
			'created_at' => $orbitalBase->getDCreation()
		]);
		$orbitalBase->setRPlace($this->connection->lastInsertId());
	}
	
	public function update($orbitalBase)
	{
		$statement = $this->connection->prepare(
			'UPDATE orbitalBase SET rPlayer = :player_id, name = :name, typeOfBase = :type,
			levelGenerator = :generator_level, levelRefinery = :refinery_level, levelDock1 = :dock1_level, levelDock2 = :dock2_level,
			levelDock3 = :dock3_level, levelTechnosphere = :technosphere_level, levelCommercialPlateforme = :commercial_platform_level,
			levelStorage = :storage_level, levelRecycling = :recycling_level, levelSpatioport = :spatioport_level, points = :points,
			iSchool = :school_investments, iAntiSpy = :anti_spy_investments, antiSpyAverage = :anti_spy_average,
			pegaseStorage = :pegase_storage, satyreStorage = :satyre_storage, sireneStorage = :sirene_storage, dryadeStorage = :dryade_storage,
			chimereStorage = :chimere_storage, meduseStorage = :meduse_storage, griffonStorage = :griffon_storage,
			cyclopeStorage = :cyclope_storage, minotaureStorage = :minotaure_storage, hydreStorage = :hydre_storage,
			cerbereStorage = :cerbere_storage, phenixStorage = :phenix_storage, resourcesStorage = :resources,
			uOrbitalBase = :u_orbital_base, dCreation = :created_at
			WHERE rPlace = :id'
		);
		$statement->execute(array(
			'player_id' => $orbitalBase->getRPlayer(),
			'name' => $orbitalBase->getName(),
			'type' => $orbitalBase->typeOfBase,
			'generator_level' => $orbitalBase->getLevelGenerator(),
			'refinery_level' => $orbitalBase->getLevelRefinery(),
			'dock1_level' => $orbitalBase->getLevelDock1(),
			'dock2_level' => $orbitalBase->getLevelDock2(),
			'dock3_level' => $orbitalBase->getLevelDock3(),
			'technosphere_level' => $orbitalBase->getLevelTechnosphere(),
			'commercial_platform_level' => $orbitalBase->getLevelCommercialPlateforme(),
			'storage_level' => $orbitalBase->getLevelStorage(),
			'recycling_level' => $orbitalBase->getLevelRecycling(),
			'spatioport_level' => $orbitalBase->getLevelSpatioport(),
			'points' => $orbitalBase->getPoints(),
			'school_investments' => $orbitalBase->getISchool(),
			'anti_spy_investments' => $orbitalBase->getIAntiSpy(),
			'anti_spy_average' => $orbitalBase->getAntiSpyAverage(),
			'pegase_storage' => $orbitalBase->getShipStorage(0),
			'satyre_storage' => $orbitalBase->getShipStorage(1),
			'sirene_storage' => $orbitalBase->getShipStorage(2),
			'dryade_storage' => $orbitalBase->getShipStorage(3),
			'chimere_storage' => $orbitalBase->getShipStorage(4),
			'meduse_storage' => $orbitalBase->getShipStorage(5),
			'griffon_storage' => $orbitalBase->getShipStorage(6),
			'cyclope_storage' => $orbitalBase->getShipStorage(7),
			'minotaure_storage' => $orbitalBase->getShipStorage(8),
			'hydre_storage' => $orbitalBase->getShipStorage(9),
			'cerbere_storage' => $orbitalBase->getShipStorage(10),
			'phenix_storage' => $orbitalBase->getShipStorage(11),
			'resources' => $orbitalBase->getResourcesStorage(),
			'u_orbital_base' => $orbitalBase->uOrbitalBase,
			'created_at' => $orbitalBase->getDCreation(),
			'id' => $orbitalBase->getRPlace(),
		));
	}
	
	public function remove($orbitalBase)
	{
		
	}
	
	public function format($data)
	{
		$orbitalBase = new OrbitalBase();
		$orbitalBase->setRPlace($data['rPlace']);
		$orbitalBase->setRPlayer($data['rPlayer']);
		$orbitalBase->setName($data['name']);
		$orbitalBase->typeOfBase = $data['typeOfBase'];
		$orbitalBase->setLevelGenerator($data['levelGenerator']);
		$orbitalBase->setLevelRefinery($data['levelRefinery']);
		$orbitalBase->setLevelDock1($data['levelDock1']);
		$orbitalBase->setLevelDock2($data['levelDock2']);
		$orbitalBase->setLevelDock3($data['levelDock3']);
		$orbitalBase->setLevelTechnosphere($data['levelTechnosphere']);
		$orbitalBase->setLevelCommercialPlateforme($data['levelCommercialPlateforme']);
		$orbitalBase->setLevelStorage($data['levelStorage']);
		$orbitalBase->setLevelRecycling($data['levelRecycling']);
		$orbitalBase->setLevelSpatioport($data['levelSpatioport']);
		$orbitalBase->setPoints($data['points']);
		$orbitalBase->setISchool($data['iSchool']);
		$orbitalBase->setIAntiSpy($data['iAntiSpy']);
		$orbitalBase->setAntiSpyAverage($data['antiSpyAverage']);
		$orbitalBase->setShipStorage(0 ,$data['pegaseStorage']);
		$orbitalBase->setShipStorage(1 ,$data['satyreStorage']);
		$orbitalBase->setShipStorage(2 ,$data['sireneStorage']);
		$orbitalBase->setShipStorage(3 ,$data['dryadeStorage']);
		$orbitalBase->setShipStorage(4 ,$data['chimereStorage']);
		$orbitalBase->setShipStorage(5 ,$data['meduseStorage']);
		$orbitalBase->setShipStorage(6 ,$data['griffonStorage']);
		$orbitalBase->setShipStorage(7 ,$data['cyclopeStorage']);
		$orbitalBase->setShipStorage(8 ,$data['minotaureStorage']);
		$orbitalBase->setShipStorage(9 ,$data['hydreStorage']);
		$orbitalBase->setShipStorage(10 ,$data['cerbereStorage']);
		$orbitalBase->setShipStorage(11 ,$data['phenixStorage']);
		$orbitalBase->setResourcesStorage($data['resourcesStorage']);
		$orbitalBase->uOrbitalBase = $data['uOrbitalBase'];
		$orbitalBase->setDCreation($data['dCreation']);

		$orbitalBase->setPosition($data['position']);
		$orbitalBase->setSystem($data['system']);
		$orbitalBase->setXSystem($data['xSystem']);
		$orbitalBase->setYSystem($data['ySystem']);
		$orbitalBase->setSector($data['sector']);
		$orbitalBase->sectorColor = $data['sectorColor'];
		$orbitalBase->setTax($data['tax']);
		$orbitalBase->setPlanetPopulation($data['planetPopulation']);
		$orbitalBase->setPlanetResources($data['planetResources']);
		$orbitalBase->setPlanetHistory($data['planetHistory']);

		$generatorTime = strtotime($data['termDateGenerator']) - strtotime(Utils::now());
		$orbitalBase->setRemainingTimeGenerator(round($generatorTime, 1));
		$dock1Time = strtotime($data['termDateDock1']) - strtotime(Utils::now());
		$orbitalBase->setRemainingTimeDock1(round($dock1Time, 1));
		$dock2Time = strtotime($data['termDateDock2']) - strtotime(Utils::now());
		$orbitalBase->setRemainingTimeDock2(round($dock2Time, 1));
		$dock3Time = strtotime($data['termDateDock3']) - strtotime(Utils::now());
		$orbitalBase->setRemainingTimeDock3(round($dock3Time, 1));

		$orbitalBase->setRoutesNumber($data['routesNumber']);
		return $orbitalBase;
	}
}