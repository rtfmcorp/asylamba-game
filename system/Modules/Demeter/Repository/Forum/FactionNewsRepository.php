<?php

namespace Asylamba\Modules\Demeter\Repository\Forum;

use Asylamba\Modules\Demeter\Model\Forum\FactionNews;

use Asylamba\Classes\Entity\AbstractRepository;

class FactionNewsRepository extends AbstractRepository
{
	
	/**
	 * @param int $id
	 * @return FactionNews
	 */
	public function get($id)
	{
		if (($fn = $this->unitOfWork->getObject(FactionNews::class, $id)) !== null) {
			return $fn;
		}
		
		$statement = $this->connection->prepare('SELECT * FROM factionNews WHERE id = :id');
		$statement->execute(['id' => $id]);
		
		if (($row = $statement->fetch()) === false) {
			return null;
		}
		$factionNew = $this->format($row);
		$this->unitOfWork->addObject($factionNew);
		return $factionNew;
	}
	
	/**
	 * @param int $factionId
	 * @return FactionNews
	 */
	public function getPinnedNew($factionId)
	{
		$statement = $this->connection->prepare('SELECT * FROM factionNews WHERE rFaction = :faction_id AND pinned = 1');
		$statement->execute(['faction_id' => $factionId]);
		
		if (($row = $statement->fetch()) === false) {
			return null;
		}
		if (($fn = $this->unitOfWork->getObject(FactionNews::class, $row['id'])) !== null) {
			return $fn;
		}
		$factionNew = $this->format($row);
		$this->unitOfWork->addObject($factionNew);
		return $factionNew;
	}
	
	/**
	 * @param int $factionId
	 * @return array
	 */
	public function getFactionNews($factionId)
	{
		$statement = $this->connection->prepare('SELECT * FROM factionNews WHERE rFaction = :faction_id');
		$statement->execute(['faction_id' => $factionId]);
		
		$data = [];
		while ($row = $statement->fetch()) {
			if (($fn = $this->unitOfWork->getObject(FactionNews::class, $row['id'])) !== null) {
				$data[] = $fn;
				continue;
			}
			$factionNew = $this->format($row);
			$this->unitOfWork->addObject($factionNew);
			$data[] = $factionNew;
		}
		return $data;
	}
	
	/**
	 * @param int $factionId
	 * @return array
	 */
	public function getFactionBasicNews($factionId)
	{
		$statement = $this->connection->prepare('SELECT * FROM factionNews WHERE rFaction = :faction_id AND pinned = 0');
		$statement->execute(['faction_id' => $factionId]);
		
		$data = [];
		while ($row = $statement->fetch()) {
			if (($fn = $this->unitOfWork->getObject(FactionNews::class, $row['id'])) !== null) {
				$data[] = $fn;
				continue;
			}
			$factionNew = $this->format($row);
			$this->unitOfWork->addObject($factionNew);
			$data[] = $factionNew;
		}
		return $data;
	}
	
	/**
	 * @param FactionNews $factionNew
	 */
	public function insert($factionNew)
	{
		$statement = $this->connection->prepare(
			'INSERT INTO factionNews SET
				rFaction = :faction_id,
				title = :title,
				oContent = :content,
				pContent = :parsed_content,
				pinned = :pinned,
				statement = :statement,
				dCreation = :created_at'
		);
		$statement->execute([
			'faction_id' => $factionNew->rFaction,
			'title' => $factionNew->title,
			'content' => $factionNew->oContent,
			'parsed_content' => $factionNew->pContent,
			'pinned' => $factionNew->pinned,
			'statement' => $factionNew->statement,
			'created_at' => Utils::now()
		]);
		$factionNew->id = $this->connection->lastInsertId();
	}
	
	/**
	 * @param FactionNews $factionNew
	 */
	public function update($factionNew)
	{
		$statement = $this->connection->prepare(
			'UPDATE factionNews SET
				rFaction = :faction_id,
				title = :title,
				oContent = :content,
				pContent = :parsed_content,
				pinned = :pinned,
				statement = :statement,
				dCreation = :created_at
			WHERE id = :id'
		);
		$statement->execute([
			'faction_id' => $factionNew->rFaction,
			'title' => $factionNew->title,
			'content' => $factionNew->oContent,
			'parsed_content' => $factionNew->pContent,
			'pinned' => $factionNew->pinned,
			'statement' => $factionNew->statement,
			'created_at' => $factionNew->dCreation,
			'id' => $factionNew->id
		]);
	}
	
	/**
	 * @param FactionNews $factionNew
	 */
	public function remove($factionNew)
	{
		$statement = $this->connection->prepare('DELETE FROM factionNews WHERE id = :id');
		$statement->execute(['id' => $factionNew->id]);
	}
	
	/**
	 * @param array $data
	 * @return FactionNews
	 */
	public function format($data)
	{
		$news = new FactionNews();
		$news->id = $data['id'];
		$news->rFaction = $data['rFaction'];
		$news->title = $data['title'];
		$news->oContent = $data['oContent'];
		$news->pContent = $data['pContent'];
		$news->pinned = $data['pinned'];
		$news->statement = $data['statement'];
		$news->dCreation = $data['dCreation'];
		return $news;
	}
}