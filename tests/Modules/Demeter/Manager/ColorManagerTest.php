<?php

namespace Tests\App\Modules\Demeter\Manager;

use App\Modules\Demeter\Manager\ColorManager;

use App\Modules\Demeter\Model\Color;
use App\Modules\Demeter\Model\Law\Law;
use App\Modules\Zeus\Model\Player;
use App\Modules\Gaia\Model\Sector;

class ColorManagerTest extends \PHPUnit\Framework\TestCase
{
	/** @var ColorManager **/
	protected $manager;
	
	public function setUp(): void
	{
		$this->manager = new ColorManager(
			$this->getEntityManagerMock(),
			$this->getPlayerManagerMock(),
			$this->getVoteManagerMock(),
			$this->getConversationManagerMock(),
			$this->getCandidateManagerMock(),
			$this->getElectionManagerMock(),
			$this->getLawManagerMock(),
			$this->getNotificationManagerMock(),
			$this->getConversationMessageManagerMock(),
			$this->getCommercialTaxManagerMock(),
			$this->getSectorManagerMock(),
			$this->getCommercialRouteManagerMock(),
			$this->getParserMock(),
			$this->getCTCMock(),
			$this->getSchedulerMock()
		);
	}
	
	public function testGet()
	{
		$faction = $this->manager->get(1);
		
		$this->assertEquals(1, $faction->getId());
		$this->assertEquals('Pokemon', $faction->getPopularName());
		$this->assertEquals('La Ligue', $faction->getOfficialName());
		$this->assertFalse($faction->getIsWinner());
		$this->assertFalse($faction->getIsClosed());
		$this->assertTrue($faction->getIsInGame());
	}
	
	public function testUpdateInfos()
	{
		$faction = $this->getFactionMock(1);
		
		$this->manager->updateInfos($faction);
		
		$this->assertEquals(18, $faction->getPlayers());
		$this->assertEquals(15, $faction->getActivePlayers());
	}
	
	public function testSendSenateNotif()
	{
		$this->assertNull($this->manager->sendSenateNotif($this->getFactionMock(1)));
		$this->assertNull($this->manager->sendSenateNotif($this->getFactionMock(2), true));
	}
	
	public function testUpdateSenate()
	{
		$this->assertNull($this->manager->updateSenate(1));
	}
	
	public function testUpdateStatus()
	{
		$players = $this->getFactionPlayersByRankingMock(1);
		
		$this->manager->updateStatus($this->getFactionMock(1), $players);
		
		$this->assertEquals(Player::PARLIAMENT, $players[0]->getStatus());
		$this->assertEquals(Player::PARLIAMENT, $players[1]->getStatus());
		$this->assertEquals(Player::PARLIAMENT, $players[5]->getStatus());
		$this->assertEquals(Player::PARLIAMENT, $players[9]->getStatus());
		$this->assertEquals(Player::STANDARD, $players[10]->getStatus());
		$this->assertEquals(Player::STANDARD, $players[11]->getStatus());
	}
	
	public function getEntityManagerMock()
	{
		$entityManagerMock = $this
			->getMockBuilder('App\Classes\Entity\EntityManager')
			->disableOriginalConstructor()
			->getMock()
		;
		$entityManagerMock
			->expects($this->any())
			->method('getRepository')
			->willReturnCallback([$this, 'getRepositoryMock'])
		;
		$entityManagerMock
			->expects($this->any())
			->method('persist')
			->willReturn(true)
		;
		$entityManagerMock
			->expects($this->any())
			->method('remove')
			->willReturn(true)
		;
		$entityManagerMock
			->expects($this->any())
			->method('flush')
			->willReturn(true)
		;
		return $entityManagerMock;
	}
	
	public function getRepositoryMock()
	{
		$repositoryMock = $this
			->getMockBuilder('App\Modules\Demeter\Repository\ColorRepository')
			->disableOriginalConstructor()
			->getMock()
		;
		$repositoryMock
			->expects($this->any())
			->method('get')
			->willReturnCallback([$this, 'getFactionMock'])
		;
		$repositoryMock
			->expects($this->any())
			->method('getAll')
			->willReturnCallback([$this, 'getAllFactionsMock'])
		;
		$repositoryMock
			->expects($this->any())
			->method('getOpenFactions')
			->willReturnCallback([$this, 'getOpenFactionsMock'])
		;
		$repositoryMock
			->expects($this->any())
			->method('getInGameFactions')
			->willReturnCallback([$this, 'getInGameFactionsMock'])
		;
		$repositoryMock
			->expects($this->any())
			->method('getAllByActivePlayersNumber')
			->willReturnCallback([$this, 'getAllByActivePlayersNumberMock'])
		;
		$repositoryMock
			->expects($this->any())
			->method('getByRegimeAndElectionStatement')
			->willReturnCallback([$this, 'getByRegimeAndElectionStatementMock'])
		;
		return $repositoryMock;
	}
	
	public function getFactionMock($id)
	{
		return
			(new Color())
			->setId($id)
			->setChiefId(1)
			->setCredits(10000)
			->setDevise('Ensemble pour la victoire !')
			->setElectionStatement(Color::MANDATE)
			->setMandateDuration(1800)
			->setPopularName('Pokemon')
			->setOfficialName('La Ligue')
			->setRegime(Color::ROYALISTIC)
			->setIsInGame(true)
			->setIsWinner(false)
			->setIsClosed(false)
			->setAlive(true)
		;
	}
	
	public function getAllFactionsMock()
	{
		return [];
	}
	
	public function getOpenFactionsMock()
	{
		return [];
	}
	
	public function getInGameFactionsMock()
	{
		return [];
	}
	
	public function getAllByActivePlayersNumberMock()
	{
		return [];
	}
	
	public function getByRegimeAndElectionStatus()
	{
		return [];
	}
	
	public function getPlayerManagerMock()
	{
		$playerManagerMock = $this
			->getMockBuilder('App\Modules\Zeus\Manager\PlayerManager')
			->disableOriginalConstructor()
			->getMock()
		;
		$playerManagerMock
			->expects($this->any())
			->method('get')
			->willReturnCallback([$this, 'getPlayerMock'])
		;
		$playerManagerMock
			->expects($this->any())
			->method('countByFactionAndStatements')
			->willReturnCallback([$this, 'getFactionPlayersCountMock'])
		;
		$playerManagerMock
			->expects($this->any())
			->method('getFactionPlayersByRanking')
			->willReturnCallback([$this, 'getFactionPlayersByRankingMock'])
		;
		$playerManagerMock
			->expects($this->any())
			->method('getParliamentMembers')
			->willReturnCallback([$this, 'getParliamentMembersMock'])
		;
		return $playerManagerMock;
	}
	
	public function getPlayerMock($id)
	{
		return
			(new Player())
			->setId($id)
			->setName('Pokahontas')
			->setBind('8zg4zef6ef')
			->setCredit(5000000)
			->setAvatar('1-5.png')
			->setLevel(5)
			->setDefeat(10)
			->setVictory(15)
			->setStatement(Player::ACTIVE)
			->setStatus(Player::PARLIAMENT)
		;
	}
	
	public function getFactionPlayersByRankingMock($factionId)
	{
		return [
			(new Player())
			->setId(156)
			->setStatus(Player::PARLIAMENT)
			->setFactionPoints(2500),
			(new Player())
			->setId(2)
			->setStatus(Player::PARLIAMENT)
			->setFactionPoints(2450),
			(new Player())
			->setId(25)
			->setStatus(Player::STANDARD)
			->setFactionPoints(2300),
			(new Player())
			->setId(45)
			->setStatus(Player::STANDARD)
			->setFactionPoints(2220),
			(new Player())
			->setId(5)
			->setStatus(Player::PARLIAMENT)
			->setFactionPoints(1987),
			(new Player())
			->setId(62)
			->setStatus(Player::PARLIAMENT)
			->setFactionPoints(1658),
			(new Player())
			->setId(70)
			->setStatus(Player::PARLIAMENT)
			->setFactionPoints(1559),
			(new Player())
			->setId(8)
			->setStatus(Player::PARLIAMENT)
			->setFactionPoints(1550),
			(new Player())
			->setId(92)
			->setStatus(Player::STANDARD)
			->setFactionPoints(1450),
			(new Player())
			->setId(10)
			->setStatus(Player::STANDARD)
			->setFactionPoints(1400),
			(new Player())
			->setId(11)
			->setStatus(Player::PARLIAMENT)
			->setFactionPoints(1398),
			(new Player())
			->setId(12)
			->setStatus(Player::PARLIAMENT)
			->setFactionPoints(1364)
		];
	}
	
	public function getParliamentMembersMock()
	{
		return [
			(new Player())->setId(1),
			(new Player())->setId(2),
			(new Player())->setId(3),
			(new Player())->setId(4),
			(new Player())->setId(5),
			(new Player())->setId(6),
			(new Player())->setId(7),
			(new Player())->setId(8),
			(new Player())->setId(9),
			(new Player())->setId(10),
		];
	}
	
	/**
	 * @param int $factionId
	 * @param array $statements
	 * @return int
	 */
	public function getFactionPlayersCountMock($factionId, $statements)
	{
		return
			(count($statements) > 1)
			? 18
			: 15
		;
	}
	
	public function getVoteManagerMock()
	{
		$voteManagerMock = $this
			->getMockBuilder('App\Modules\Demeter\Manager\Election\VoteManager')
			->disableOriginalConstructor()
			->getMock()
		;
		return $voteManagerMock;
	}
	
	public function getConversationManagerMock()
	{
		$conversationManagerMock = $this
			->getMockBuilder('App\Modules\Hermes\Manager\ConversationManager')
			->disableOriginalConstructor()
			->getMock()
		;
		return $conversationManagerMock;
	}
	
	public function getCandidateManagerMock()
	{
		$candidateManagerMock = $this
			->getMockBuilder('App\Modules\Demeter\Manager\Election\CandidateManager')
			->disableOriginalConstructor()
			->getMock()
		;
		return $candidateManagerMock;
	}
	
	public function getElectionManagerMock()
	{
		$electionManagerMock = $this
			->getMockBuilder('App\Modules\Demeter\Manager\Election\ElectionManager')
			->disableOriginalConstructor()
			->getMock()
		;
		return $electionManagerMock;
	}
	
	public function getLawManagerMock()
	{
		$lawManagerMock = $this
			->getMockBuilder('App\Modules\Demeter\Manager\Law\LawManager')
			->disableOriginalConstructor()
			->getMock()
		;
		$lawManagerMock
			->expects($this->any())
			->method('getByFactionAndStatements')
			->willReturnCallback([$this, 'getLawsMock'])
		;
		$lawManagerMock
			->expects($this->any())
			->method('ballot')
			->willReturn(true)
		;
		return $lawManagerMock;
	}
	
	public function getLawsMock($factionId)
	{
		return [
			(new Law())
			->setId(1)
			->setFactionId($factionId)
			->setStatement(Law::VOTATION)
			->setType(Law::PEACEPACT)
			->setOptions(['rColor' => 2])
			->setCreatedAt((new \DateTime('-2 days'))->format('Y-m-d H:i:s'))
			->setVotedAt((new \DateTime('-2 hours'))->format('Y-m-d H:i:s')),
			(new Law())
			->setId(2)
			->setFactionId($factionId)
			->setStatement(Law::VOTATION)
			->setType(Law::SECTORNAME)
			->setOptions(['rSector' => 1])
			->setCreatedAt((new \DateTime('-2 days'))->format('Y-m-d H:i:s'))
			->setVotedAt((new \DateTime('+2 hours'))->format('Y-m-d H:i:s')),
			(new Law())
			->setId(3)
			->setFactionId($factionId)
			->setStatement(Law::EFFECTIVE)
			->setType(Law::TECHNOLOGYTRANSFER)
			->setCreatedAt((new \DateTime('-2 days'))->format('Y-m-d H:i:s'))
			->setVotedAt((new \DateTime('-3 hours'))->format('Y-m-d H:i:s'))
			->setEndedAt((new \DateTime('+7 days'))->format('Y-m-d H:i:s'))
		];
	}
	
	public function getNotificationManagerMock()
	{
		$notificationManagerMock = $this
			->getMockBuilder('App\Modules\Hermes\Manager\NotificationManager')
			->disableOriginalConstructor()
			->getMock()
		;
		$notificationManagerMock
			->expects($this->any())
			->method('add')
			->willReturn(true)
		;
		return $notificationManagerMock;
	}
	
	public function getConversationMessageManagerMock()
	{
		$conversationMessageManagerMock = $this
			->getMockBuilder('App\Modules\Hermes\Manager\ConversationMessageManager')
			->disableOriginalConstructor()
			->getMock()
		;
		return $conversationMessageManagerMock;
	}
	
	public function getCommercialTaxManagerMock()
	{
		$commercialTaxManagerMock = $this
			->getMockBuilder('App\Modules\Athena\Manager\CommercialTaxManager')
			->disableOriginalConstructor()
			->getMock()
		;
		return $commercialTaxManagerMock;
	}
	
	public function getSectorManagerMock()
	{
		$sectorManagerMock = $this
			->getMockBuilder('App\Modules\Gaia\Manager\SectorManager')
			->disableOriginalConstructor()
			->getMock()
		;
		$sectorManagerMock
			->expects($this->any())
			->method('get')
			->willReturnCallback([$this, 'getSectorMock'])
		;
		return $sectorManagerMock;
	}
	
	public function getSectorMock($id)
	{
		return
			(new Sector())
			->setId($id)
			->setName('Secteur ' . $id)
			->setLifePlanet(500)
			->setPoints(2)
			->setPopulation(155000200)
			->setRColor(1)
			->setPrime(false)
			->setTax(5)
				
		;
	}
	
	public function getCommercialRouteManagerMock()
	{
		$commercialRouteManagerMock = $this
			->getMockBuilder('App\Modules\Athena\Manager\CommercialRouteManager')
			->disableOriginalConstructor()
			->getMock()
		;
		return $commercialRouteManagerMock;
	}
	
	public function getParserMock()
	{
		$parserMock = $this
			->getMockBuilder('App\Classes\Library\Parser')
			->disableOriginalConstructor()
			->getMock()
		;
		return $parserMock;
	}
	
	public function getCTCMock()
	{
		$ctcMock = $this
			->getMockBuilder('App\Classes\Worker\CTC')
			->disableOriginalConstructor()
			->getMock()
		;
		$ctcMock
			->expects($this->any())
			->method('createContext')
			->willReturn(true)
		;
		$ctcMock
			->expects($this->any())
			->method('add')
			->willReturn(true)
		;
		$ctcMock
			->expects($this->any())
			->method('applyContext')
			->willReturn(true)
		;
		return $ctcMock;
	}
	
	public function getSchedulerMock()
	{
		$schedulerMock = $this
			->getMockBuilder('App\Classes\Scheduler\RealTimeActionScheduler')
			->disableOriginalConstructor()
			->getMock()
		;
		$schedulerMock
			->expects($this->any())
			->method('schedule')
			->willReturn(true)
		;
		return $schedulerMock;
	}
}
