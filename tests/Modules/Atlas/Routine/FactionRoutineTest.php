<?php

namespace Tests\Asylamba\Modules\Atlas\Routine;

use Asylamba\Modules\Atlas\Routine\FactionRoutine;

use Asylamba\Modules\Demeter\Model\Color;
use Asylamba\Modules\Gaia\Model\Sector;
use Asylamba\Modules\Atlas\Model\PlayerRanking;
use Asylamba\Modules\Atlas\Model\FactionRanking;
use Asylamba\Modules\Zeus\Model\Player;
use Asylamba\Modules\Demeter\Resource\ColorResource;
use Asylamba\Modules\Atlas\Model\Ranking;

class FactionRoutineTest extends \PHPUnit\Framework\TestCase
{
	/** @var FactionRoutine **/
	protected $routine;
	/** @var array **/
	protected $rankings;
	/** @var array **/
	protected $rankingPoints = [
		1 => 140,
		2 => 142,
		4 => 130,
		5 => 135,
	];
	
	public function setUp(): void
	{
		$this->routine = new FactionRoutine();
		$this->rankings = [];
	}
	
	public function testExecute()
	{
		$faction = $this->getFactionMock(1);
		
		$this->routine->execute($faction, $this->getPlayerRankingsMock(1), $this->getRoutesIncomeMock(1), $this->getSectorsMock());
		
		$results = $this->routine->getResults();
		
		$this->assertCount(1, $results);
		$this->assertArrayHasKey(1, $results);
		$this->assertEquals(11634, $results[1]['general']);
		$this->assertEquals(22560, $results[1]['wealth']);
		$this->assertEquals(5, $results[1]['territorial']);
		$this->assertEquals(140, $results[1]['points']);
	}
	
	public function testProcessResults()
	{
		$factions = [
			$this->getFactionMock(1),
			$this->getFactionMock(2),
			$this->getFactionMock(4),
			$this->getFactionMock(5)
		];
		$this->routine->execute($factions[0], $this->getPlayerRankingsMock(1), $this->getRoutesIncomeMock(1), $this->getSectorsMock());
		$this->routine->execute($factions[1], $this->getPlayerRankingsMock(2), $this->getRoutesIncomeMock(3), $this->getSectorsMock());
		$this->routine->execute($factions[2], $this->getPlayerRankingsMock(4), $this->getRoutesIncomeMock(2), $this->getSectorsMock());
		$this->routine->execute($factions[3], $this->getPlayerRankingsMock(5), $this->getRoutesIncomeMock(4), $this->getSectorsMock());
		
		$this->routine->processResults($this->getRankingMock(), $factions, $this->getFactionRankingManagerMock());
		
		$this->assertCount(4, $this->rankings);
		$this->assertEquals(1, $this->rankings[0]->getFactionId());
		$this->assertEquals(165, $this->rankings[0]->getPoints());
		$this->assertEquals(3, $this->rankings[0]->getPointsPosition());
		$this->assertEquals(-1, $this->rankings[0]->getPointsVariation());
		$this->assertEquals(25, $this->rankings[0]->getNewPoints());
		$this->assertEquals(11634, $this->rankings[0]->getGeneral());
		$this->assertEquals(4, $this->rankings[0]->getGeneralPosition());
		$this->assertEquals(-3, $this->rankings[0]->getGeneralVariation());
		$this->assertEquals(22560, $this->rankings[0]->getWealth());
		$this->assertEquals(4, $this->rankings[0]->getWealthPosition());
		$this->assertEquals(-1, $this->rankings[0]->getWealthVariation());
		$this->assertEquals(5, $this->rankings[0]->getTerritorial());
		$this->assertEquals(1, $this->rankings[0]->getTerritorialPosition());
		$this->assertEquals(3, $this->rankings[0]->getTerritorialVariation());
		
		$this->assertEquals(2, $this->rankings[1]->getFactionId());
		$this->assertEquals(171, $this->rankings[1]->getPoints());
		$this->assertEquals(1, $this->rankings[1]->getPointsPosition());
		$this->assertEquals(0, $this->rankings[1]->getPointsVariation());
		$this->assertEquals(29, $this->rankings[1]->getNewPoints());
		$this->assertEquals(12757, $this->rankings[1]->getGeneral());
		$this->assertEquals(3, $this->rankings[1]->getGeneralPosition());
		$this->assertEquals(-1, $this->rankings[1]->getGeneralVariation());
		$this->assertEquals(67680, $this->rankings[1]->getWealth());
		$this->assertEquals(2, $this->rankings[1]->getWealthPosition());
		$this->assertEquals(-1, $this->rankings[1]->getWealthVariation());
		$this->assertEquals(1, $this->rankings[1]->getTerritorial());
		$this->assertEquals(2, $this->rankings[1]->getTerritorialPosition());
		$this->assertEquals(0, $this->rankings[1]->getTerritorialVariation());
		
		$this->assertEquals(4, $this->rankings[2]->getFactionId());
		$this->assertEquals(156, $this->rankings[2]->getPoints());
		$this->assertEquals(4, $this->rankings[2]->getPointsPosition());
		$this->assertEquals(0, $this->rankings[2]->getPointsVariation());
		$this->assertEquals(26, $this->rankings[2]->getNewPoints());
		$this->assertEquals(15003, $this->rankings[2]->getGeneral());
		$this->assertEquals(2, $this->rankings[2]->getGeneralPosition());
		$this->assertEquals(1, $this->rankings[2]->getGeneralVariation());
		$this->assertEquals(45120, $this->rankings[2]->getWealth());
		$this->assertEquals(3, $this->rankings[2]->getWealthPosition());
		$this->assertEquals(-1, $this->rankings[2]->getWealthVariation());
		$this->assertEquals(1, $this->rankings[2]->getTerritorial());
		$this->assertEquals(2, $this->rankings[2]->getTerritorialPosition());
		$this->assertEquals(1, $this->rankings[2]->getTerritorialVariation());
		
		$this->assertEquals(5, $this->rankings[3]->getFactionId());
		$this->assertEquals(170, $this->rankings[3]->getPoints());
		$this->assertEquals(2, $this->rankings[3]->getPointsPosition());
		$this->assertEquals(1, $this->rankings[3]->getPointsVariation());
		$this->assertEquals(35, $this->rankings[3]->getNewPoints());
		$this->assertEquals(16126, $this->rankings[3]->getGeneral());
		$this->assertEquals(1, $this->rankings[3]->getGeneralPosition());
		$this->assertEquals(3, $this->rankings[3]->getGeneralVariation());
		$this->assertEquals(90240, $this->rankings[3]->getWealth());
		$this->assertEquals(1, $this->rankings[3]->getWealthPosition());
		$this->assertEquals(3, $this->rankings[3]->getWealthVariation());
		$this->assertEquals(1, $this->rankings[3]->getTerritorial());
		$this->assertEquals(2, $this->rankings[3]->getTerritorialPosition());
		$this->assertEquals(-1, $this->rankings[3]->getTerritorialVariation());
	}
	
	public function getFactionMock($id)
	{
		return
			(new Color())
			->setId($id)
			->setOfficialName(ColorResource::getInfo($id, 'officialName'))
			->setPopularName(ColorResource::getInfo($id, 'popularName'))
			->setDevise(ColorResource::getInfo($id, 'devise'))
			->setPlayers(11)
			->setActivePlayers(8)
			->setChiefId(3)
			->setCredits(15000)
			->setIsInGame(true)
			->setIsClosed(false)
			->setIsWinner(false)
			->setRankingPoints($this->rankingPoints[$id])
		;
	}
	
	public function getRankingMock()
	{
		return
			(new Ranking())
			->setId(1)
		;
	}
	
	public function getFactionRankingManagerMock()
	{
		$factionRankingManager = $this
			->getMockBuilder('Asylamba\Modules\Atlas\Manager\FactionRankingManager')
			->disableOriginalConstructor()
			->getMock()
		;
		$factionRankingManager
			->expects($this->any())
			->method('size')
			->willReturn(4)
		;
		$factionRankingManager
			->expects($this->any())
			->method('get')
			->willReturnCallback([$this, 'getFactionRankingMock'])
		;
		$factionRankingManager
			->expects($this->any())
			->method('add')
			->willReturnCallback([$this, 'storeFactionRanking'])
		;
		return $factionRankingManager;
	}
	
	public function getFactionRankingMock($id)
	{
		return [
			(new FactionRanking())
			->setPoints(140)
			->setFactionId(1)
			->setPointsPosition(2)
			->setGeneralPosition(1)
			->setWealthPosition(3)
			->setTerritorialPosition(4),
			(new FactionRanking())
			->setPoints(142)
			->setFactionId(2)
			->setPointsPosition(1)
			->setGeneralPosition(2)
			->setWealthPosition(1)
			->setTerritorialPosition(2),
			(new FactionRanking())
			->setPoints(130)
			->setFactionId(4)
			->setPointsPosition(4)
			->setGeneralPosition(3)
			->setWealthPosition(2)
			->setTerritorialPosition(3),
			(new FactionRanking())
			->setPoints(135)
			->setFactionId(5)
			->setPointsPosition(3)
			->setGeneralPosition(4)
			->setWealthPosition(4)
			->setTerritorialPosition(1),
		][$id];
	}
	
	public function storeFactionRanking(FactionRanking $factionRanking)
	{
		$this->rankings[] = $factionRanking;
	}
	
	public function getPlayerRankingsMock($factionId)
	{
		return [
			(new PlayerRanking())
			->setPlayer(
				(new Player())
				->setRColor($factionId)
			)
			->setGeneral(1423 * $factionId),
			(new PlayerRanking())
			->setPlayer(
				(new Player())
				->setRColor($factionId)
			)
			->setGeneral(482),
			(new PlayerRanking())
			->setPlayer(
				(new Player())
				->setRColor($factionId)
			)
			->setGeneral(1560),
			(new PlayerRanking())
			->setPlayer(
				(new Player())
				->setRColor($factionId)
			)
			->setGeneral(100),
			(new PlayerRanking())
			->setPlayer(
				(new Player())
				->setRColor($factionId)
			)
			->setGeneral(2123 - ($factionId * 200)),
			(new PlayerRanking())
			->setPlayer(
				(new Player())
				->setRColor($factionId)
			)
			->setGeneral(5165),
			(new PlayerRanking())
			->setPlayer(
				(new Player())
				->setRColor($factionId)
			)
			->setGeneral(548 - ($factionId * 100)),
			(new PlayerRanking())
			->setPlayer(
				(new Player())
				->setRColor($factionId)
			)
			->setGeneral(533)
		];
	}
	
	public function getRoutesIncomeMock($coeff)
	{
		return [
			'nb' => 6,
			'income' => 22560 * $coeff
		];
	}
	
	public function getSectorsMock()
	{
		return [
			(new Sector())
			->setId(1)
			->setRColor(1)
			->setPoints(1),
			(new Sector())
			->setId(2)
			->setRColor(2)
			->setPoints(1),
			(new Sector())
			->setId(3)
			->setRColor(0)
			->setPoints(1),
			(new Sector())
			->setId(4)
			->setRColor(0)
			->setPoints(1),
			(new Sector())
			->setId(5)
			->setRColor(1)
			->setPoints(1),
			(new Sector())
			->setId(6)
			->setRColor(4)
			->setPoints(1),
			(new Sector())
			->setId(7)
			->setRColor(5)
			->setPoints(1),
			(new Sector())
			->setId(8)
			->setRColor(1)
			->setPoints(1),
			(new Sector())
			->setId(9)
			->setRColor(0)
			->setPoints(1),
			(new Sector())
			->setId(10)
			->setRColor(0)
			->setPoints(1),
			(new Sector())
			->setId(11)
			->setRColor(1)
			->setPoints(2),
		];
	}
}
