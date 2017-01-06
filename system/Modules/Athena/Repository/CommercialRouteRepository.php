<?php

namespace Asylamba\Modules\Athena\Repository;

use Asylamba\Classes\Entity\AbstractRepository;

use Asylamba\Modules\Athena\Model\CommercialRoute;

class CommercialRouteRepository extends AbstractRepository {
	
	public function select($where, $params)
	{
		$query = $this->connection->prepare(
			'SELECT cr.id AS id,
			cr.rOrbitalBase AS rOrbitalBase,
			cr.rOrbitalBaseLinked AS rOrbitalBaseLinked,
			cr.imageLink AS imageLink,
			cr.distance AS distance,
			cr.price AS price,
			cr.income AS income,
			cr.dProposition AS dProposition,
			cr.dCreation AS dCreation,
			cr.statement AS statement,

			ob1.rPlayer AS playerId1,
			ob1.name AS baseName1,
			ob1.typeOfBase AS baseType1,
			pl1.name AS playerName1,
			pl1.rColor AS playerColor1,
			pl1.avatar AS avatar1,
			p1.population AS population1,

			ob2.rPlayer AS playerId2,
			ob2.name AS baseName2,
			ob2.typeOfBase AS baseType2,
			pl2.name AS playerName2,
			pl2.rColor AS playerColor2,
			pl2.avatar AS avatar2,
			p2.population AS population2

			FROM commercialRoute AS cr

			LEFT JOIN orbitalBase AS ob1 ON cr.rOrbitalBase = ob1.rPlace
			LEFT JOIN player AS pl1 ON ob1.rPlayer = pl1.id
			LEFT JOIN place AS p1 ON ob1.rPlace = p1.id

			LEFT JOIN orbitalBase AS ob2 ON cr.rOrbitalBaseLinked = ob2.rPlace
			LEFT JOIN player AS pl2 ON ob2.rPlayer = pl2.id
			LEFT JOIN place AS p2 ON ob2.rPlace = p2.id
			WHERE ' . $where
		);
		$query->execute($params);
		return $query;
	}
	
	/**
	 * @param int $id
	 * @return CommercialRoute
	 */
	public function get($id)
	{
		$query = $this->select('cr.id = :id', ['id' => $id]);
		
		if (($row = $query->fetch()) === false) {
			return null;
		}
		$commercialRoute = $this->format($row);
		$this->unitOfWork->addObject($commercialRoute);
		return $commercialRoute;
	}
	
	/**
	 * @param int $id
	 * @param int $baseId
	 * @return CommercialRoute
	 */
	public function getByIdAndBase($id, $baseId)
	{
		$query = $this->select(
			'cr.id = :id AND rOrbitalBase = :base_id',
			['id' => $id, 'base_id' => $baseId]);
		
		if (($row = $query->fetch()) === false) {
			return null;
		}
		$commercialRoute = $this->format($row);
		$this->unitOfWork->addObject($commercialRoute);
		return $commercialRoute;
	}
	
	/**
	 * @param int $id
	 * @param int $baseId
	 * @return CommercialRoute
	 */
	public function getByIdAndDistantBase($id, $baseId)
	{
		$query = $this->select(
			'cr.id = :id AND rOrbitalBaseLinked = :distant_base_id',
			['id' => $id, 'distant_base_id' => $baseId]);
		
		if (($row = $query->fetch()) === false) {
			return null;
		}
		$commercialRoute = $this->format($row);
		$this->unitOfWork->addObject($commercialRoute);
		return $commercialRoute;
	}
	
	/**
	 * @param int $baseId
	 * @return array
	 */
	public function getByBase($baseId)
	{
		$query = $this->select('rOrbitalBase = :base_id', ['base_id' => $baseId]);
		
		$data = [];
		while($row = $query->fetch()) {
			$commercialRoute = $this->format($row);
			$this->unitOfWork->addObject($commercialRoute);
			$data[] = $commercialRoute;
		}
		return $data;
	}
	
	/**
	 * @param int $baseId
	 * @return array
	 */
	public function getByDistantBase($baseId)
	{
		$query = $this->select('rOrbitalBaseLinked = :base_id', ['base_id' => $baseId]);
		
		$data = [];
		while($row = $query->fetch()) {
			$commercialRoute = $this->format($row);
			$this->unitOfWork->addObject($commercialRoute);
			$data[] = $commercialRoute;
		}
		return $data;
	}
	
	/**
	 * @param int $baseId
	 * @param int $distantBaseId
	 * @return array
	 */
	public function getExistingRoute($baseId, $distantBaseId)
	{
		$query = $this->select(
			'(rOrbitalBase = :base_id1 AND rOrbitalBaseLinked = :distant_base_id1) OR
			(rOrbitalBase = :distant_base_id2 AND rOrbitalBaseLinked = :base_id2)'
		, [
			'base_id1' => $baseId,
			'base_id2' => $baseId,
			'distant_base_id1' => $distantBaseId,
			'distant_base_id2' => $distantBaseId
		]);
		
		$data = [];
		while($row = $query->fetch()) {
			$commercialRoute = $this->format($row);
			$this->unitOfWork->addObject($commercialRoute);
			$data[] = $commercialRoute;
		}
		return $data;
	}
	
	/**
	 * @param int $baseId
	 * @return int
	 */
	public function getBaseIncome($baseId)
	{
		$query = $this->connection->prepare(
			'SELECT SUM(income) AS total_income FROM commercialRoute
			WHERE (rOrbitalBase = :base_id OR rOrbitalBaseLinked = :distant_base_id) AND statement = :active_statement '
		);
		$query->execute([
			'base_id' => $baseId,
			'distant_base_id' => $baseId,
			'active_statement' => CommercialRoute::ACTIVE
		]);
		if (($result = $query->fetch()) === false) {
			return 0;
		} 
		return (int) $result['total_income'];
	}
	
	/**
	 * @param int $baseId
	 * @param int $distantBaseId
	 * @return bool
	 */
	public function isAlreadyARoute($baseId, $distantBaseId)
	{
		$query = $this->connection->prepare(
			'SELECT COUNT(*) as nb_routes FROM commercialRoute
			WHERE (rOrbitalBase = :base_id1 AND rOrbitalBaseLinked = :distant_base_id1) OR
			(rOrbitalBase = :distant_base_id2 AND rOrbitalBaseLinked = :base_id2)'
		);
		$query->execute([
			'base_id1' => $baseId,
			'base_id2' => $baseId,
			'distant_base_id1' => $distantBaseId,
			'distant_base_id2' => $distantBaseId
		]);
		return (bool) $query->fetch()['nb_routes'];
	}
	
	/**
	 * @param int $baseId
	 * @return int
	 */
	public function countBaseActiveAndStandbyRoutes($baseId)
	{
		$query = $this->connection->prepare(
			'SELECT COUNT(*) as nb_routes FROM commercialRoute 
			WHERE (rOrbitalBase = :base_id OR rOrbitalBaseLinked = :distant_base_id)
			AND statement IN (:active_statement, :stand_by_statement)'
		);
		$query->execute([
			'base_id' => $baseId,
			'distant_base_id' => $baseId,
			'active_statement' => CommercialRoute::ACTIVE,
			'stand_by_statement' => CommercialRoute::STANDBY
		]);
		return (int) $query->fetch()['nb_routes'];
	}
	
	/**
	 * @param int $baseId
	 * @return int
	 */
	public function countBaseActiveRoutes($baseId)
	{
		$query = $this->connection->prepare(
			'SELECT COUNT(*) as nb_routes FROM commercialRoute 
			WHERE (rOrbitalBase = :base_id OR rOrbitalBaseLinked = :distant_base_id)
			AND statement = :active_statement'
		);
		$query->execute([
			'base_id' => $baseId,
			'distant_base_id' => $baseId,
			'active_statement' => CommercialRoute::ACTIVE
		]);
		return (int) $query->fetch()['nb_routes'];
	}
	
	/**
	 * @param int $baseId
	 * @return int
	 */
	public function countBaseRoutes($baseId)
	{
		$query = $this->connection->prepare(
			'SELECT COUNT(*) as nb_routes FROM commercialRoute 
			WHERE rOrbitalBase = :base_id OR rOrbitalBaseLinked = :distant_base_id'
		);
		$query->execute(['base_id' => $baseId, 'distant_base_id' => $baseId]);
		return (int) $query->fetch()['nb_routes'];
	}
	
	/**
	 * @param CommercialRoute $commercialRoute
	 */
	public function insert($commercialRoute)
	{
		$query = $this->connection->prepare('INSERT INTO
			commercialRoute(rOrbitalBase, rOrbitalBaseLinked, imageLink, distance, price, income, dProposition, dCreation, statement)
			VALUES(:orbital_base, :destination_base, :image_link, :distance, :price, :income, :proposition, :created_at, :statement)');
		$query->execute(array(
			'orbital_base' => $commercialRoute->getROrbitalBase(),
			'destination_base' => $commercialRoute->getROrbitalBaseLinked(),
			'image_link' => $commercialRoute->getImageLink(),
			'distance' => $commercialRoute->getDistance(),
			'price' => $commercialRoute->getPrice(),
			'income' => $commercialRoute->getIncome(),
			'proposition' => $commercialRoute->getDProposition(),
			'created_at' => $commercialRoute->getDCreation(),
			'statement' => $commercialRoute->getStatement()
		));
		$commercialRoute->setId($this->connection->lastInsertId());
	}
	
	/**
	 * @param CommercialRoute $commercialRoute
	 */
	public function update($commercialRoute)
	{
		$query = $this->connection->prepare(
			'UPDATE commercialRoute SET
			rOrbitalBase = :orbital_base, rOrbitalBaseLinked = :destination_base, imageLink = :image_link,
			distance = :distance, price = :price, income = :income, dProposition = :proposition,
			dCreation = :created_at, statement = :statement
			WHERE id = :id'
		);
		$query->execute(array(
			'orbital_base' => $commercialRoute->getROrbitalBase(),
			'destination_base' => $commercialRoute->getROrbitalBaseLinked(),
			'image_link' => $commercialRoute->getImageLink(),
			'distance' => $commercialRoute->getDistance(),
			'price' => $commercialRoute->getPrice(),
			'income' => $commercialRoute->getIncome(),
			'proposition' => $commercialRoute->getDProposition(),
			'created_at' => $commercialRoute->getDCreation(),
			'statement' => $commercialRoute->getStatement(),
			'id' => $commercialRoute->getId()
		));
	}
	
	/**
	 * @param CommercialRoute $commercialRoute
	 */
	public function remove($commercialRoute)
	{
		$query = $this->connection->prepare('DELETE FROM commercialRoute WHERE id = :id');
		$query->execute(['id' => $commercialRoute->id]);
	}
	
	/**
	 * @param array $data
	 * @return CommercialRoute
	 */
	public function format($data)
	{
		$commercialRoute = new CommercialRoute();
		$commercialRoute->setId((int) $data['id']);
		$commercialRoute->setROrbitalBase((int) $data['rOrbitalBase']);
		$commercialRoute->setROrbitalBaseLinked((int) $data['rOrbitalBaseLinked']);
		$commercialRoute->setImageLink($data['imageLink']);
		$commercialRoute->setDistance($data['distance']);
		$commercialRoute->setPrice((int) $data['price']);
		$commercialRoute->setIncome((int) $data['income']);
		$commercialRoute->setDProposition($data['dProposition']);
		$commercialRoute->setDCreation($data['dCreation']);
		$commercialRoute->setStatement((int) $data['statement']);

		$commercialRoute->setBaseName1($data['baseName1']);
		$commercialRoute->baseType1 = $data['baseType1'];
		$commercialRoute->setPlayerId1($data['playerId1']);
		$commercialRoute->setPlayerName1($data['playerName1']);
		$commercialRoute->playerColor1 = $data['playerColor1'];
		$commercialRoute->setAvatar1($data['avatar1']);
		$commercialRoute->setPopulation1($data['population1']);

		$commercialRoute->setBaseName2($data['baseName2']);
		$commercialRoute->baseType2 = $data['baseType2'];
		$commercialRoute->setPlayerId2($data['playerId2']);
		$commercialRoute->setPlayerName2($data['playerName2']);
		$commercialRoute->playerColor2 = $data['playerColor2'];
		$commercialRoute->setAvatar2($data['avatar2']);
		$commercialRoute->setPopulation2($data['population2']);
		
		return $commercialRoute;
	}
}