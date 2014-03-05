<?php
class MotherShip {

	//ATTRIBUTES : MOTHERSHIP
	private $id;
	private $uId;
	private $rPlayer;
	private $rPlace;
	private $rFleet1 = 0;
	private $rFleet2 = 0;
	private $rFleet3 = 0;
	private $name;
	private $type;
	private $isInStorage = 1;
	private $levelRefinery = 1;
	private $levelDock = 0;
	private $levelHistoricalCenter = 0;
	private $levelGateway = 0;
	private $resourcesStorage = 0;
	private $pegaseStorage = 0;
	private $satyreStorage  = 0;
	private $chimereStorage = 0;
	private $sireneStorage  = 0;
	private $dryadeStorage  = 0;
	private $meduseStorage  = 0;
	private $creationDate;
	//ATTRIBUTES : PLACE
	private $position;
	private $system;
	private $xSystem;
	private $ySystem;
	private $sector;
	private $tax;
	private $typeOfPlace;
	private $placeResources;
	private $placeHistory;
	//ATTRIBUTES : OTHERS
	private $remainingTimeBuilding;
	private $remainingTimeDock;
	private $buildingQueueArray;
	private $dockQueueArray;
	private $fleetsArray;


	//GETTERS
	public function getId() { return $this->id; }
	public function getUId() { return $this->uId; }
	public function getRPlayer() { return $this->rPlayer ; }
	public function getRPlace() { return $this->rPlace ; }
	public function getName() { return $this->name ; }
	public function getType() { return $this->type ; }
	public function getIsInStorage() { return $this->isInStorage ; }
	public function getLevelRefinery() { return $this->levelRefinery ; }
	public function getLevelDock() { return $this->levelDock ; }
	public function getLevelHistoricalCenter() { return $this->levelHistoricalCenter ; }
	public function getLevelGateway() { return $this->levelGateway ; }
	public function getResourcesStorage() { return $this->resourcesStorage ; }
	public function getPegaseStorage() { return $this->pegaseStorage ; }
	public function getSatyreStorage () { return $this->satyreStorage ; }
	public function getChimereStorage() { return $this->chimereStorage ; }
	public function getSireneStorage () { return $this->sireneStorage ; }
	public function getDryadeStorage () { return $this->dryadeStorage ; }
	public function getMeduseStorage () { return $this->meduseStorage ; }
	public function getCreationDate() { return $this->creationDate ; }
	
	public function getPosition() { return $this->position ; }
	public function getSystem() { return $this->system ; }
	public function getXSystem() { return $this->xSystem; }
	public function getYSystem() { return $this->ySystem; }
	public function getSector() { return $this->sector ; }
	public function getTax() { return $this->tax ; }
	public function getTypeOfPlace() { return $this->typeOfPlace; }
	public function getPlaceResources() { return $this->placeResources ; }
	public function getPlaceHistory() { return $this->placeHistory ; }

	public function getRemainingTimeBuilding() { return $this->remainingTimeBuilding; }
	public function getRemainingTimeDock() { return $this->remainingTimeDock; }
	public function getBuildingQueueArray() { return $this->buildingQueueArray; }
	public function getDockQueueArray() { return $this->dockQueueArray; }
	public function getFleetsArray() { return $this->fleetsArray; }

	//SETTERS
	public function setRPlayer($var) { $this->rPlayer = $var; }
	public function setRPlace($var) { $this->rPlace = $var; }
	public function setName($var) { $this->name = $var; }
	public function setType($var) { $this->type = $var; }
	public function setIsInStorage($var) { $this->isInStorage = $var; }
	public function setLevelRefinery($var) { $this->levelRefinery = $var; }
	public function setLevelDock($var) { $this->levelDock = $var; }
	public function setLevelHistoricalCenter($var) { $this->levelHistoricalCenter = $var; }
	public function setLevelGateway($var) { $this->levelGateway = $var; }
	public function setResourcesStorage($var) { $this->resourcesStorage = $var; }
	public function setPegaseStorage($var) { $this->pegaseStorage = $var; }
	public function setSatyreStorage($var) { $this->satyreStorage = $var; }
	public function setChimereStorage($var) { $this->chimereStorage = $var; }
	public function setSireneStorage($var) { $this->sireneStorage = $var; }
	public function setDryadeStorage($var) { $this->dryadeStorage = $var; }
	public function setMeduseStorage($var) { $this->meduseStorage = $var; }

	//METHODS
	public function create() {
		if($this->rPlayer != NULL AND $this->rPlace != NULL AND $this->name != NULL AND $this->type != NULL) {
			$db = DataBase::getInstance();
			$qr = $db->prepare('INSERT INTO 
				motherShip(uId, rPlayer, rPlace, rFleet1, rFleet2, rFleet3, name, type, inStorage, 
					levelRefinery, levelDock, levelHistoricalCenter, levelGateway, 
					resourcesStorage, pegaseStorage, satyreStorage, chimereStorage, sireneStorage, dryadeStorage, meduseStorage, creationDate)
				VALUES(?, ?, ?, 0, 0, 0, ?, ?, ?, 
					?, ?, ?, ?, 
					?, ?, ?, ?, ?, ?, ?, NOW())
				');
			
			$aw = $qr->execute(array(
				uniqid(), $this->rPlayer, $this->rPlace, $this->name, $this->type, $this->isInStorage,
				$this->levelRefinery, $this->levelDock, $this->levelHistoricalCenter, $this->levelGateway,
				$this->resourcesStorage, $this->pegaseStorage, $this->satyreStorage, $this->chimereStorage, $this->sireneStorage, $this->dryadeStorage, $this->meduseStorage
			));
			if($this->isInStorage == 0) {
				$db = DataBase::getInstance();
				$qr = $db->prepare('UPDATE place
					SET rPlayer = ?
					WHERE id = ?
				');
				$qr->execute(array($this->rPlayer, $this->rPlace));
			} else if($this->isInStorage == 1) {
				// mettre "motherShip" à 1 dans la table orbitalBase
				$db = DataBase::getInstance();
				$qr = $db->prepare('UPDATE orbitalBase
					SET motherShip = 1
					WHERE rPlayer = ? AND rPlace = ?
				');
				$qr->execute(array($this->rPlayer, $this->rPlace));
			}
		} else {
			$_SESSION[SERVERSESS]['alert'][] = array(3, 'You have to define "rPlayer", "rPlace", "name" and "type" to create a motherShip');
		}
	}

	public function delete() {
		if($this->id != NULL AND $this->uId != NULL AND $this->rPlayer != NULL AND $this->rPlace != NULL AND $this->name != NULL) {
			$db = DataBase::getInstance();
			$qr = $db->prepare('DELETE FROM motherShip
				WHERE id = ?');
			$qr->execute(array($this->id));
		} else {
			$_SESSION[SERVERSESS]['alert'][] = array(3, 'You have to define "id", "uId", rPlayer", "rPlace" and "name" to delete this orbitalBase');
		}
	}

	public function read($uId) {
		$this->uId = $uId;
		$db = DataBase::getInstance(); 
		$qr = $db->prepare('SELECT 
				ms.*,
				p.position AS position,
				p.rSystem AS system,
				s.xPosition AS xSystem,
				s.yPosition AS ySystem,
				s.rSector AS sector,
				se.tax AS tax,
				p.typeOfPlace AS typeOfPlace,
				p.ressources AS placeResources,
				p.history AS placeHistory,
				(SELECT
					SUM(bq.remainingTime) 
					FROM motherShipBuildingQueue AS bq 
					WHERE bq.rMotherShip = ms.id)
					AS remainingTimeBuilding,
				(SELECT 
					SUM(sq.remainingTime) 
					FROM motherShipShipQueue AS sq 
					WHERE sq.rMotherShip = ms.id) 
					AS remainingTimeDock
			FROM motherShip AS ms
			LEFT JOIN place AS p
				ON ms.rPlace = p.id
			LEFT JOIN system AS s
				ON p.rSystem = s.id
			LEFT JOIN sector AS se
				ON s.rSector = se.id
			WHERE ms.uId = ? 
		');
		$qr->execute(array($this->uId));
		$aw = $qr->fetch();

		if (empty($aw)) {
			$_SESSION[SERVERSESS]['alert'][] = array(2, 'There\'s nothing for this base');
			return FALSE;
		} else {
			$this->id = $aw['id'];
			$this->rPlayer = $aw['rPlayer'];
			$this->rPlace = $aw['rPlace'];
			$this->rFleet1 = $aw['rFleet1'];
			$this->rFleet2 = $aw['rFleet2'];
			$this->rFleet3 = $aw['rFleet3'];
			$this->name = $aw['name'];
			$this->type = $aw['type'];
			$this->isInStorage = $aw['inStorage'];
			$this->levelRefinery = $aw['levelRefinery'];
			$this->levelDock = $aw['levelDock'];
			$this->levelHistoricalCenter = $aw['levelHistoricalCenter'];
			$this->levelGateway = $aw['levelGateway'];
			$this->resourcesStorage = $aw['resourcesStorage'];
			$this->pegaseStorage = $aw['pegaseStorage'];
			$this->satyreStorage = $aw['satyreStorage'];
			$this->chimereStorage = $aw['chimereStorage'];
			$this->sireneStorage = $aw['sireneStorage'];
			$this->dryadeStorage = $aw['dryadeStorage'];
			$this->meduseStorage = $aw['meduseStorage'];
			$this->creationDate = $aw['creationDate'];

			$this->position = $aw['position'];
			$this->system = $aw['system'];
			$this->xSystem = $aw['xSystem'];
			$this->ySystem = $aw['ySystem'];
			$this->sector = $aw['sector'];
			$this->tax = $aw['tax'];
			$this->typeOfPlace = $aw['typeOfPlace'];
			$this->placeResources = $aw['placeResources'];
			$this->placeHistory = $aw['placeHistory'];

			$this->remainingTimeBuilding = round($aw['remainingTimeBuilding'], 1);
			$this->remainingTimeDock = round($aw['remainingTimeDock'], 1);

			$this->readBuildingQueueState();
			$this->readDockQueueState();
			if($this->rFleet1 != 0 OR $this->rFleet2 != 0 OR $this->rFleet3 != 0) {
				$this->readFleets();
			}

			return TRUE;
		}
	}

	public function readBuildingQueueState() {
		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT 
				buildingNumber,
				remainingTime
			FROM motherShipBuildingQueue
			WHERE rMotherShip = ?
			ORDER BY id
		');
		$qr->execute(array($this->id));
		$aw = $qr->fetchAll();

		if (empty($aw)) {
			//$_SESSION[SERVERSESS]['alert'][] = array(2, 'Nothing\'s in the building queue');
			return FALSE;
		} else {
			//Bug::pre($aw);
			$this->buildingQueueArray = $aw;
			return TRUE;
		}
	}

	public function readDockQueueState() {
		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT 
				shipNumber,
				remainingTime
			FROM motherShipShipQueue
			WHERE rMotherShip = ?
			ORDER BY id
		');
		$qr->execute(array($this->id));
		$aw = $qr->fetchAll();

		if (empty($aw)) {
			//$_SESSION[SERVERSESS]['alert'][] = array(2, 'Nothing\'s in the dock1 ship queue');
			return FALSE;
		} else {
			//Bug::pre($aw);
			$this->dockQueueArray = $aw;
			return TRUE;
		}
	}

	public function readFleets() {
		$db = DataBase::getInstance();
		$qr = $db->prepare('SELECT *
			FROM commander
			WHERE (id = ? OR id = ? OR id = ?) AND rPlayer = ?
			ORDER BY id
		');
		$qr->execute(array($this->rFleet1, $this->rFleet2, $this->rFleet3, $this->rPlayer));
		$aw = $qr->fetchAll();

		if (empty($aw)) {
			//$_SESSION[SERVERSESS]['alert'][] = array(2, 'No fleet');
			return FALSE;
		} else {
			//Bug::pre($aw);
			$this->fleetsArray = $aw;
			return TRUE;
		}
	}

	public function startBuildingConstruction($buildingNumber) {
		if (MotherShipResource::isABuilding($buildingNumber)) {
			$db = DataBase::getInstance();
			$qr = $db->prepare('SELECT 
				COUNT(id) 
				FROM motherShipBuildingQueue
				WHERE rMotherShip = ? AND buildingNumber = ?
			');
			$qr->execute(array($this->id, $buildingNumber));
			$inConstruction = $qr->fetch();

			$db = DataBase::getInstance();
			$qr = $db->prepare('
				SELECT 
					level' . ucfirst(MotherShipResource::getBuildingInfo($buildingNumber, 'name')) . ' 
				FROM motherShip
				WHERE id = ?
			');
			$qr->execute(array($this->id));
			$currentLevel = $qr->fetch();

			$currentLevel[0] += $inConstruction[0];

			$remainingTime = MotherShipResource::getBuildingInfo($buildingNumber, 'level', $currentLevel[0]+1, 'time');
			$resourcePrice = MotherShipResource::getBuildingInfo($buildingNumber, 'level', $currentLevel[0]+1, 'resourcePrice');
			if ($this->resourcesStorage >= $resourcePrice) {
				if ($remainingTime != FALSE) {
					$db = DataBase::getInstance();
					$qr = $db->prepare('
						INSERT INTO 
						motherShipBuildingQueue(rMotherShip, buildingNumber, targetLevel, remainingTime)
						VALUES(?, ?, ?, ?)
						');
					
					$aw = $qr->execute(array(
						$this->id,
						$buildingNumber,
						$currentLevel[0]+1,
						$remainingTime,
					));

					$this->debitResource($resourcePrice);

				} else {
					$_SESSION[SERVERSESS]['alert'][] = array(2, 'There is a problem with getting $remainingTime');
				}
			} else {
				$_SESSION[SERVERSESS]['alert'][] = array(2, 'You don\'t have enough credit or resources to build this !');
			}
		} else {
			$_SESSION[SERVERSESS]['alert'][] = array(2, 'the building number ' . $buildingNumber . ' is not a mother ship building !');
		}
	}


	public function startShipConstruction($shipNumber) {
		if (MotherShipResource::isAShipFromDock($shipNumber)) {
			$remainingTime = ShipResource::getInfo($shipNumber, 'time');
			$resourcePrice = ShipResource::getInfo($shipNumber, 'resourcePrice');
			
			if ($this->resourcesStorage >= $resourcePrice) {
				if ($remainingTime != FALSE) {

					$db = DataBase::getInstance();
					$qr = $db->prepare('
						INSERT INTO 
						motherShipShipQueue(rMotherShip, shipNumber, remainingTime)
						VALUES(?, ?, ?)
						');
					
					$aw = $qr->execute(array(
						$this->id,
						$shipNumber,
						$remainingTime,
					));

					$this->debitResource($resourcePrice);

				} else {
					$_SESSION[SERVERSESS]['alert'][] = array(2, 'There is a problem with getting $remainingTime');
				}
			} else {
				$_SESSION[SERVERSESS]['alert'][] = array(2, 'You don\'t have enough credit or resources to build this !');
			}
		} else {
			$_SESSION[SERVERSESS]['alert'][] = array(2, 'This dock can only create a ship with id between 0 and 5');
		}
	}

	private function debitCredit($credits) {
		Controller::setPlayerInfo('credit', Controller::getPlayerInfo('credit') - $credits);
	}

	private function debitResource($resources) {
		$db = DataBase::getInstance();
		$qr = $db->prepare('UPDATE motherShip
			SET resourcesStorage = ?
			WHERE id = ?
		');
		$qr->execute(array($this->resourcesStorage - $resources, $this->id));
	}
}
?>