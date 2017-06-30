<?php

namespace Asylamba\Modules\Zeus\Repository;

use Asylamba\Classes\Entity\AbstractRepository;

use Asylamba\Modules\Zeus\Model\Player;

class PlayerRepository extends AbstractRepository {
	
	public function get($id)
	{
		if (($p = $this->unitOfWork->getObject(Player::class, $id)) !== null) {
			return $p;
		}
		$query = $this->connection->prepare('SELECT * FROM player WHERE id = :id');
		$query->execute(['id' => $id]);
		
		if (($row = $query->fetch()) === false) {
			return null;
		}
		$player = $this->format($row);
		$this->unitOfWork->addObject($player);
		return $player;
	}
	
	public function getByName($name)
	{
		$query = $this->connection->prepare('SELECT * FROM player WHERE name = :name');
		$query->execute(['name' => $name]);
		
		if (($row = $query->fetch()) === false) {
			return null;
		}
		if (($p = $this->unitOfWork->getObject(Player::class, (int) $row['id'])) !== null) {
			return $p;
		}
		$player = $this->format($row);
		$this->unitOfWork->addObject($player);
		return $player;
	}
	
	public function getByBindKey($bindKey)
	{
		$query = $this->connection->prepare('SELECT * FROM player WHERE bind = :bind_key');
		$query->execute(['bind_key' => $bindKey]);
		
		if (($row = $query->fetch()) === false) {
			return null;
		}
		if (($p = $this->unitOfWork->getObject(Player::class, (int) $row['id'])) !== null) {
			return $p;
		}
		$player = $this->format($row);
		$this->unitOfWork->addObject($player);
		return $player;
	}
	
	public function getGodSons($playerId)
	{
		$query = $this->connection->prepare('SELECT * FROM player WHERE rGodFather = :god_father_id');
		$query->execute(['god_father_id' => $playerId]);
		
		$data = [];
		while ($row = $query->fetch()) {
			if (($p = $this->unitOfWork->getObject(Player::class, (int) $row['id'])) !== null) {
				$data[] = $p;
				continue;
			}
			$player = $this->format($row);
			$this->unitOfWork->addObject($player);
			$data[] = $player;
		}
		return $data;
	}
	
	public function getByIdsAndStatements($ids, $statements)
	{
		$query = $this->connection->prepare('SELECT * FROM player WHERE id IN (' . implode(',', $ids) . ') AND statement IN (' . implode(',', $statements) . ')');
		$query->execute();
		
		$data = [];
		while ($row = $query->fetch()) {
			if (($p = $this->unitOfWork->getObject(Player::class, (int) $row['id'])) !== null) {
				$data[] = $p;
				continue;
			}
			$player = $this->format($row);
			$this->unitOfWork->addObject($player);
			$data[] = $player;
		}
		return $data;
	}
	
	/**
	 * @param array $statements
	 */
	public function getByStatements($statements)
	{
		$query = $this->connection->query('SELECT * FROM player WHERE statement IN (' . implode(',', $statements) . ')');
		
		$data = [];
		while ($row = $query->fetch()) {
			if (($p = $this->unitOfWork->getObject(Player::class, (int) $row['id'])) !== null) {
				$data[] = $p;
				continue;
			}
			$player = $this->format($row);
			$this->unitOfWork->addObject($player);
			$data[] = $player;
		}
		return $data;
	}
	
	/**
	 * @return int
	 */
	public function countActivePlayers()
	{
		$query = $this->connection->prepare('SELECT COUNT(*) as nb_players FROM player WHERE statement = :statement_active');
		$query->execute(['statement_active' => Player::ACTIVE]);
		return (int) $query->fetch()['nb_players'];
	}
	
	/**
	 * @return int
	 */
	public function countAllPlayers()
	{
		$query = $this->connection->prepare('SELECT COUNT(*) as nb_players FROM player WHERE statement IN (:statement_active, :statement_inactive)');
		$query->execute(['statement_active' => Player::ACTIVE, 'statement_inactive' => Player::INACTIVE]);
		return (int) $query->fetch()['nb_players'];
	}
	
	/**
	 * @param int $factionId
	 * @param array $statements
	 * @return int
	 */
	public function countByFactionAndStatements($factionId, $statements)
	{
		$query = $this->connection->prepare('SELECT COUNT(*) as nb_players FROM player WHERE rColor = :faction_id AND statement IN (' . implode(',', $statements) . ')');
		$query->execute(['faction_id' => $factionId]);
		return (int) $query->fetch()['nb_players'];
	}
	
	/**
	 * @param int $factionId
	 * @return array
	 */
	public function getFactionPlayers($factionId)
	{
		$query = $this->connection->prepare('SELECT * FROM player WHERE rColor = :faction_id AND statement != :dead_statement');
		$query->execute(['faction_id' => $factionId, 'dead_statement' => Player::DEAD]);
		
		$data = [];
		while ($row = $query->fetch()) {
			if (($p = $this->unitOfWork->getObject(Player::class, (int) $row['id'])) !== null) {
				$data[] = $p;
				continue;
			}
			$player = $this->format($row);
			$this->unitOfWork->addObject($player);
			$data[] = $player;
		}
		return $data;
	}
	
	/**
	 * @param int $factionId
	 * @return array
	 */
	public function getFactionPlayersByRanking($factionId)
	{
		$query = $this->connection->prepare('SELECT * FROM player WHERE rColor = :faction_id AND statement != :dead_statement ORDER BY factionPoint DESC');
		$query->execute(['faction_id' => $factionId, 'dead_statement' => Player::DEAD]);
		
		$data = [];
		while ($row = $query->fetch()) {
			if (($p = $this->unitOfWork->getObject(Player::class, (int) $row['id'])) !== null) {
				$data[] = $p;
				continue;
			}
			$player = $this->format($row);
			$this->unitOfWork->addObject($player);
			$data[] = $player;
		}
		return $data;
	}
	
	/**
	 * @param int $factionId
	 * @return array
	 */
	public function getFactionPlayersByName($factionId)
	{
		$query = $this->connection->prepare(
			'SELECT * FROM player WHERE rColor = :faction_id AND statement IN (' .
			implode(',', [Player::ACTIVE, Player::INACTIVE, Player::HOLIDAY]) . ')
			ORDER BY name ASC'
		);
		$query->execute(['faction_id' => $factionId]);
		
		$data = [];
		while ($row = $query->fetch()) {
			if (($p = $this->unitOfWork->getObject(Player::class, (int) $row['id'])) !== null) {
				$data[] = $p;
				continue;
			}
			$player = $this->format($row);
			$this->unitOfWork->addObject($player);
			$data[] = $player;
		}
		return $data;
	}
	
	/**
	 * 
	 * @param int $factionId
	 * @return Player
	 */
	public function getFactionAccount($factionId)
	{
		$query = $this->connection->prepare('SELECT * FROM player WHERE rColor = :faction_id AND status = :dead_status ORDER BY id ASC LIMIT 0,1');
		$query->execute(['faction_id' => $factionId, 'dead_status' => Player::DEAD]);
		
		if (($row = $query->fetch()) === false) {
			return null;
		}
		if (($p = $this->unitOfWork->getObject(Player::class, (int) $row['id'])) !== null) {
			return $p;
		}
		$player = $this->format($row);
		$this->unitOfWork->addObject($player);
		return $player;
	}
	
	/**
	 * @param int $factionId
	 * @return array
	 */
	public function getLastFactionPlayers($factionId)
	{
		$query = $this->connection->prepare('SELECT * FROM player WHERE rColor = :faction_id AND statement != :dead_statement ORDER BY dInscription DESC LIMIT 0,25');
		$query->execute(['faction_id' => $factionId, 'dead_statement' => Player::DEAD]);
		
		$data = [];
		while ($row = $query->fetch()) {
			if (($p = $this->unitOfWork->getObject(Player::class, (int) $row['id'])) !== null) {
				$data[] = $p;
				continue;
			}
			$player = $this->format($row);
			$this->unitOfWork->addObject($player);
			$data[] = $player;
		}
		return $data;
	}

	/**
	 * @param int $factionId
	 * @return array
	 */
	public function getParliamentMembers($factionId)
	{
		$query = $this->connection->prepare('SELECT * FROM player WHERE rColor = :faction_id AND status = :status');
		$query->execute(['faction_id' => $factionId, 'status' => Player::PARLIAMENT]);
		
		$data = [];
		while ($row = $query->fetch()) {
			if (($p = $this->unitOfWork->getObject(Player::class, (int) $row['id'])) !== null) {
				$data[] = $p;
				continue;
			}
			$player = $this->format($row);
			$this->unitOfWork->addObject($player);
			$data[] = $player;
		}
		return $data;
	}
	
	/**
	 * @param int $factionId
	 * @param int $status
	 * @return Player
	 */
	public function getGovernmentMember($factionId, $status)
	{
		$query = $this->connection->prepare('SELECT * FROM player WHERE rColor = :faction_id AND status = :status AND statement != :dead_statement');
		$query->execute(['faction_id' => $factionId, 'status' => $status, 'dead_statement' => Player::DEAD]);
		
		if (($row = $query->fetch()) === false) {
			return null;
		}
		if (($p = $this->unitOfWork->getObject(Player::class, (int) $row['id'])) !== null) {
			return $p;
		}
		$player = $this->format($row);
		$this->unitOfWork->addObject($player);
		return $player;
	}
	
	/**
	 * @param int $factionId
	 * @return array
	 */
	public function getGovernmentMembers($factionId)
	{
		$query = $this->connection->prepare(
			'SELECT * FROM player WHERE rColor = :faction_id AND statement != :dead_statement AND status IN (' . implode(',', [Player::TREASURER, Player::WARLORD, Player::MINISTER, Player::CHIEF]) . ') ORDER BY status DESC'
		);
		$query->execute(['faction_id' => $factionId, 'dead_statement' => Player::DEAD]);
		
		$data = [];
		while ($row = $query->fetch()) {
			if (($p = $this->unitOfWork->getObject(Player::class, (int) $row['id'])) !== null) {
				$data[] = $p;
				continue;
			}
			$player = $this->format($row);
			$this->unitOfWork->addObject($player);
			$data[] = $player;
		}
		return $data;
	}
	
	/**
	 * @param int $factionId
	 * @return Player
	 */
	public function getFactionLeader($factionId)
	{
		$query = $this->connection->prepare('SELECT * FROM player WHERE rColor = :faction_id AND status = :status AND statement != :dead_statement');
		$query->execute(['faction_id' => $factionId, 'status' => Player::CHIEF, 'dead_statement' => Player::DEAD]);
		
		if (($row = $query->fetch()) === false) {
			return null;
		}
		if (($p = $this->unitOfWork->getObject(Player::class, (int) $row['id'])) !== null) {
			return $p;
		}
		$player = $this->format($row);
		$this->unitOfWork->addObject($player);
		return $player;
	}
	
	public function getActivePlayers()
	{
		$query = $this->connection->prepare(
			'SELECT * FROM player WHERE statement = :statement'
		);
		$query->execute(['statement' => Player::ACTIVE]);
		
		$data = [];
		while ($row = $query->fetch()) {
			if (($p = $this->unitOfWork->getObject(Player::class, (int) $row['id'])) !== null) {
				$data[] = $p;
				continue;
			}
			$player = $this->format($row);
			$this->unitOfWork->addObject($player);
			$data[] = $player;
		}
		return $data;
	}
	
	public function search($search)
	{
		$query = $this->connection->prepare(
			'SELECT p.* FROM player AS p WHERE LOWER(name) LIKE LOWER(:search) ORDER BY experience DESC LIMIT 0,20'
		);
		$query->execute(['search' => "%$search%"]);
		
		$data = [];
		while ($row = $query->fetch()) {
			if (($p = $this->unitOfWork->getObject(Player::class, (int) $row['id'])) !== null) {
				$data[] = $p;
				continue;
			}
			$player = $this->format($row);
			$this->unitOfWork->addObject($player);
			$data[] = $player;
		}
		return $data;
	}
	
	/**
	 * @param Player $player
	 */
	public function insert($player)
	{
		$query = $this->connection->prepare('INSERT INTO
			player(bind, rColor, name, sex, description, avatar, status, rGodfather,
			credit, uPlayer, experience, factionPoint, level, victory, defeat, stepTutorial,
			stepDone, iUniversity, partNaturalSciences, partLifeSciences, partSocialPoliticalSciences,
			partInformaticEngineering, dInscription, dLastConnection, dLastActivity, premium, statement)
			VALUES(:bind, :faction_id, :name, :gender, :description, :avatar, :status,
			:god_father_id, :credits, :u_player, :experience, :faction_points, :level,
			:nb_victories, :nb_defeats, :tutorial_step, :tutorial_step_is_done, :university_investment,
			:natural_science_investment, :life_science_investment, :social_political_science_investment,
			:informatic_engineering_investment, :created_at, :last_connected_at, :last_acted_at, :is_premium, :statement)');
		$query->execute(array(
			'bind' => $player->getBind(),
			'faction_id' => $player->getRColor(),
			'name' => $player->getName(),
			'gender' => $player->sex,
			'description' => $player->description,
			'avatar' => $player->getAvatar(),
			'status' => $player->getStatus(),
			'god_father_id' => $player->rGodfather,
			'credits' => $player->getCredit(),
			'u_player' => $player->uPlayer,
			'experience' => $player->getExperience(),
			'faction_points' => $player->factionPoint,
			'level' => $player->getLevel(),
			'nb_victories' => $player->getVictory(),
			'nb_defeats' => $player->getDefeat(),
			'tutorial_step' => $player->getStepTutorial(),
			'tutorial_step_is_done' => $player->stepDone,
			'university_investment' => $player->iUniversity,
			'natural_science_investment' => $player->partNaturalSciences,
			'life_science_investment' => $player->partLifeSciences,
			'social_political_science_investment' => $player->partSocialPoliticalSciences,
			'informatic_engineering_investment' => $player->partInformaticEngineering,
			'created_at' => $player->getDInscription(),
			'last_connected_at' => $player->getDLastConnection(),
			'last_acted_at' => $player->getDLastActivity(),
			'is_premium' => $player->getPremium(),
			'statement' => $player->getStatement()
		));
		$player->setId((int) $this->connection->lastInsertId());
	}
	
	/**
	 * @param Player $player
	 */
	public function update($player)
	{
		$query = $this->connection->prepare('UPDATE player SET 
			description = :description,
			status = :status,
			uPlayer = :u_player,
			experience = :experience,
			factionPoint = :faction_points,
			level = :level,
			victory = :nb_victories,
			defeat = :nb_defeats,
			stepTutorial = :tutorial_step,
			stepDone = :tutorial_step_is_done,
			partNaturalSciences = :natural_science_investment,
			partLifeSciences = :life_science_investment,
			partSocialPoliticalSciences = :social_political_investment,
			partInformaticEngineering = :informatic_engineering_investment,
			dLastConnection = :last_connected_at,
			dLastActivity = :last_acted_at,
			statement = :statement
		WHERE id = :id');
		$query->execute(array(
			'description' => $player->description,
			'status' => $player->getStatus(),
			'u_player' => $player->uPlayer,
			'experience' => $player->getExperience(),
			'faction_points' => $player->factionPoint,
			'level' => $player->getLevel(),
			'nb_victories' => $player->getVictory(),
			'nb_defeats' => $player->getDefeat(),
			'tutorial_step' => $player->getStepTutorial(),
			'tutorial_step_is_done' => $player->stepDone,
			'natural_science_investment' => $player->partNaturalSciences,
			'life_science_investment' => $player->partLifeSciences,
			'social_political_investment' => $player->partSocialPoliticalSciences,
			'informatic_engineering_investment' => $player->partInformaticEngineering,
			'last_connected_at' => $player->getDLastConnection(),
			'last_acted_at' => $player->getDLastActivity(),
			'statement' => $player->getStatement(),
			'id' => $player->getId()
		));
	}
	
	/**
	 * @param Player $player
	 * @param int $credits
	 * @param string $operator
	 */
	public function updatePlayerCredits(Player $player, $credits, $operator)
	{
		$query = $this->connection->prepare("UPDATE player SET credit = credit $operator $credits, uPlayer = :updated_at WHERE id = :id");
		$query->execute(array(
			'updated_at' => $player->uPlayer,
			'id' => $player->getId()
		));
	}
	
	/**
	 * @param int $playerId
	 * @param int $investment
	 */
	public function updateUniversityInvestment($playerId, $investment)
	{
		$query = $this->connection->prepare("UPDATE player SET iUniversity = :investment WHERE id = :id");
		$query->execute(array(
			'investment' => $investment,
			'id' => $playerId
		));
	}
	
	public function remove($entity)
	{
		
	}
	
	public function format($data)
	{
		$player = new Player();

		$player->setId((int) $data['id']);
		$player->setBind($data['bind']);
		$player->setRColor((int) $data['rColor']);
		$player->setName($data['name']);
		$player->sex = $data['sex'];
		$player->description = $data['description'];
		$player->setAvatar($data['avatar']);
		$player->setStatus((int) $data['status']);
		$player->rGodfather = $data['rGodfather'];
		$player->setCredit((int) $data['credit']);
		$player->uPlayer = $data['uPlayer'];
		$player->setExperience((int) $data['experience']);
		$player->factionPoint = (int) $data['factionPoint'];
		$player->setLevel((int) $data['level']);
		$player->setVictory((int) $data['victory']);
		$player->setDefeat((int) $data['defeat']);
		$player->setStepTutorial((int) $data['stepTutorial']);
		$player->stepDone = (int) $data['stepDone'];
		$player->iUniversity = (int) $data['iUniversity'];
		$player->partNaturalSciences = (int) $data['partNaturalSciences'];
		$player->partLifeSciences = (int) $data['partLifeSciences'];
		$player->partSocialPoliticalSciences = (int) $data['partSocialPoliticalSciences'];
		$player->partInformaticEngineering = (int) $data['partInformaticEngineering'];
		$player->setDInscription($data['dInscription']);
		$player->setDLastConnection($data['dLastConnection']);
		$player->setDLastActivity($data['dLastActivity']);
		$player->setPremium($data['premium']);
		$player->setStatement($data['statement']);
		
		return $player;
	}
}