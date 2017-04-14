<?php

namespace Asylamba\Modules\Gaia\Repository;

use Asylamba\Classes\Entity\AbstractRepository;

use Asylamba\Modules\Gaia\Model\Place;

class PlaceRepository extends AbstractRepository
{
	public function select($whereClause = '', $parameters = [])
	{
		$statement = $this->connection->prepare(
			'SELECT p.*,
			s.rSector AS rSector,
			s.xPosition AS xPosition,
			s.yPosition AS yPosition,
			s.typeOfSystem AS typeOfSystem,
			se.tax AS tax,
			se.rColor AS sectorColor,
			pl.rColor AS playerColor,
			pl.name AS playerName,
			pl.avatar AS playerAvatar,
			pl.status AS playerStatus,
			pl.level AS playerLevel,
			ob.rPlace AS obId,
			ob.name AS obName,
			ob.points AS points,
			ob.levelCommercialPlateforme AS levelCommercialPlateforme,
			ob.levelSpatioport AS levelSpatioport,
			ob.resourcesStorage AS obResources,
			ob.antiSpyAverage AS antiSpyAverage,
			ob.typeOfBase AS obTypeOfBase
			FROM place AS p
			LEFT JOIN system s ON p.rSystem = s.id
			LEFT JOIN sector se ON s.rSector = se.id
			LEFT JOIN player pl ON p.rPlayer = pl.id
			LEFT JOIN orbitalBase ob ON p.id = ob.rPlace '
			. $whereClause
		);
		$statement->execute($parameters);
		
		return $statement;
	}
	
	/**
	 * @param int $id
	 * @return Place
	 */
	public function get($id)
	{
		if (($p = $this->unitOfWork->getObject(Place::class, $id)) !== null) {
			return $p;
		}
		$statement = $this->select('WHERE p.id = :id', ['id' => $id]);
		
		if (($row = $statement->fetch()) === false) {
			return null;
		}
		$place = $this->format($row);
		$this->unitOfWork->addObject($place);
		return $place;
	}
	
	/**
	 * @param array $ids
	 * @return array
	 */
	public function getByIds($ids)
	{
		$statement = $this->select('WHERE p.id IN (' . implode(',', $ids) . ')');
		
		$data = [];
		while ($row = $statement->fetch()) {
			if (($p = $this->unitOfWork->getObject(Place::class, $row['id'])) !== null) {
				$data[] = $p;
				continue;
			}
			$place = $this->format($row);
			$this->unitOfWork->addObject($place);
			$data[] = $place;
		}
		return $data;
	}
	
	/**
	 * @param array $systemId
	 * @return array
	 */
	public function getSystemPlaces($systemId)
	{
		$statement = $this->select('WHERE p.rSystem = :system_id ' . $this->getOrderByClause(['position' => 'ASC']), ['system_id' => $systemId]);
		
		$data = [];
		while ($row = $statement->fetch()) {
			if (($p = $this->unitOfWork->getObject(Place::class, $row['id'])) !== null) {
				$data[] = $p;
				continue;
			}
			$place = $this->format($row);
			$this->unitOfWork->addObject($place);
			$data[] = $place;
		}
		return $data;
	}
	
	/**
	 * @param string $search
	 * @return array
	 */
	public function search($search)
	{
		$statement = $this->select(
			'WHERE (pl.statement = 1 OR pl.statement = 2 OR pl.statement = 3)
			AND (LOWER(pl.name) LIKE LOWER(:place_name)
			OR LOWER(ob.name) LIKE LOWER(:base_name)) ' . $this->getOrderByClause(['pl.id' => 'DESC']) . ' LIMIT 20',
			['place_name' => "%$search%", 'base_name' => "%$search%"]
		);
		
		$data = [];
		while ($row = $statement->fetch()) {
			if (($p = $this->unitOfWork->getObject(Place::class, $row['id'])) !== null) {
				$data[] = $p;
				continue;
			}
			$place = $this->format($row);
			$this->unitOfWork->addObject($place);
			$data[] = $place;
		}
		return $data;
	}
	
	public function insert($place)
	{
		$qr = $this->connection->prepare(
			'INSERT INTO place(rPlayer, rSystem, typeOfPlace, position, population,
			coefResources, coefHistory, resources, danger, maxDanger, uPlace)
			VALUES(:player_id, :system_id, :place_type, :position, :population,
			:resources_coeff, :history_coeff, :resources, :danger, :maxDanger, :uPlace)'
		);
		$qr->execute(array(
			'player_id' => $place->getRPlayer(),
			'system_id' => $place->getRSystem(),
			'place_type' => $place->getTypeOfPlace(),
			'position' => $place->getPosition(),
			'population' => $place->getPopulation(),
			'resources_coeff' => $place->getCoefResources(),
			'history_coeff' => $place->getCoefHistory(),
			'resources' => $place->getResources(),
			'danger' => $place->danger,
			'maxDanger' => $place->maxDanger,
			'uPlace' => $place->uPlace
		));
		$place->setId($this->connection->lastInsertId());
	}
	
	public function update($place)
	{
		$statement = $this->connection->prepare(
			'UPDATE place SET
				rPlayer = :player_id,
				rSystem = :system_id,
				typeOfPlace = :place_type,
				position = :position,
				population = :population,
				coefResources = :resources_coeff,
				coefHistory = :history_coeff,
				resources = :resources,
				danger = :danger,
				maxDanger = :max_danger,
				uPlace = :u_place
			WHERE id = :id');
		$statement->execute(array(
			'player_id' => $place->getRPlayer(),
			'system_id' => $place->getRSystem(),
			'place_type' => $place->getTypeOfPlace(),
			'position' => $place->getPosition(),
			'population' => $place->getPopulation(),
			'resources_coeff' => $place->getCoefResources(),
			'history_coeff' => $place->getCoefHistory(),
			'resources' => $place->getResources(),
			'danger' => $place->danger,
			'max_danger' => $place->maxDanger,
			'u_place' => $place->uPlace,
			'id' => $place->getId()
		));
	}
	
	public function remove($place)
	{
		$statement = $this->connection->prepare('DELETE FROM place FROM id = :id');
		$statement->execute(['id' => $place->getId()]);
	}
	
	public function format($data)
	{
		$place = new Place();

		$place->setId($data['id']);
		$place->setRSystem($data['rSystem']);
		$place->setTypeOfPlace($data['typeOfPlace']);
		$place->setPosition($data['position']);
		$place->setPopulation($data['population']);
		$place->setCoefResources($data['coefResources']);
		$place->setCoefHistory($data['coefHistory']);
		$place->setResources($data['resources']);
		$place->danger = $data['danger'];
		$place->maxDanger = $data['maxDanger'];
		$place->uPlace = $data['uPlace'];

		$place->setRSector($data['rSector']);
		$place->setXSystem($data['xPosition']);
		$place->setYSystem($data['yPosition']);
		$place->setTypeOfSystem($data['typeOfSystem']);
		$place->setTax($data['tax']);
		$place->setSectorColor($data['sectorColor']);

		if ($data['rPlayer'] != 0) {
			$place->setRPlayer($data['rPlayer']);
			$place->setPlayerColor($data['playerColor']);
			$place->setPlayerName($data['playerName']);
			$place->setPlayerAvatar($data['playerAvatar']);
			$place->setPlayerStatus($data['playerStatus']);
			$place->playerLevel = $data['playerLevel'];
			if (isset($data['msId'])) {
				$place->setTypeOfBase($data['msType']);
				$place->setBaseName($data['msName']);
				$place->setResources($data['msResources']);
			} elseif (isset($data['obId'])) {
				$place->setTypeOfBase(Place::TYP_ORBITALBASE);
				$place->typeOfOrbitalBase = $data['obTypeOfBase'];
				$place->setBaseName($data['obName']);
				$place->setLevelCommercialPlateforme($data['levelCommercialPlateforme']);
				$place->setLevelSpatioport($data['levelSpatioport']);
				$place->setResources($data['obResources']);
				$place->setAntiSpyInvest($data['antiSpyAverage']);
				$place->setPoints($data['points']);
			} else {
				throw new ErrorException('Problèmes d\'appartenance du lieu !');
			}
		} else {
			$place->setTypeOfBase(Place::TYP_EMPTY);
			$place->setBaseName('Planète rebelle');
			$place->setPoints(0);
		}
		return $place;
	}
}