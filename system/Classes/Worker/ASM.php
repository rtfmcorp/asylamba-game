<?php

namespace Asylamba\Classes\Worker;

use Asylamba\Modules\Ares\Manager\CommanderManager;
use Asylamba\Modules\Ares\Manager\ReportManager;
use Asylamba\Modules\Ares\Manager\LittleReportManager;

use Asylamba\Modules\Artemis\Manager\SpyReportManager;

use Asylamba\Modules\Athena\Manager\BuildingQueueManager;
use Asylamba\Modules\Athena\Manager\CommercialRouteManager;
use Asylamba\Modules\Athena\Manager\CommercialShippingManager;
use Asylamba\Modules\Athena\Manager\CommercialTaxManager;
use Asylamba\Modules\Athena\Manager\OrbitalBaseManager;
use Asylamba\Modules\Athena\Manager\RecyclingLogManager;
use Asylamba\Modules\Athena\Manager\RecyclingMissionManager;
use Asylamba\Modules\Athena\Manager\ShipQueueManager;
use Asylamba\Modules\Athena\Manager\TransactionManager;

use Asylamba\Modules\Atlas\Manager\FactionRankingManager;
use Asylamba\Modules\Atlas\Manager\PlayerRankingManager;

use Asylamba\Modules\Demeter\Manager\Election\CandidateManager;
use Asylamba\Modules\Demeter\Manager\Election\ElectionManager;
use Asylamba\Modules\Demeter\Manager\Election\VoteManager;
use Asylamba\Modules\Demeter\Manager\Forum\FactionNewsManager;
use Asylamba\Modules\Demeter\Manager\Forum\ForumTopicManager;
use Asylamba\Modules\Demeter\Manager\Law\LawManager;
use Asylamba\Modules\Demeter\Manager\Law\VoteLawManager;
use Asylamba\Modules\Demeter\Manager\ColorManager;
use Asylamba\Modules\Demeter\Manager\Forum\ForumMessageManager;

use Asylamba\Modules\Gaia\Manager\PlaceManager;
use Asylamba\Modules\Gaia\Manager\SectorManager;
use Asylamba\Modules\Gaia\Manager\SystemManager;

use Asylamba\Modules\Hermes\Manager\ConversationManager;
use Asylamba\Modules\Hermes\Manager\ConversationMessageManager;
use Asylamba\Modules\Hermes\Manager\ConversationUserManager;
use Asylamba\Modules\Hermes\Manager\NotificationManager;
use Asylamba\Modules\Hermes\Manager\RoadMapManager;

use Asylamba\Modules\Promethee\Manager\ResearchManager;
use Asylamba\Modules\Promethee\Manager\TechnologyQueueManager;

use Asylamba\Modules\Zeus\Manager\CreditTransactionManager;
use Asylamba\Modules\Zeus\Manager\PlayerManager;

abstract class ASM {
	public static $runningAres = FALSE;
	public static $com;
	public static $rpm;
	public static $lrm;

	protected static $runningAthena = FALSE;
	public static $bqm;
	public static $crm;
	public static $obm;
	public static $sqm;
	public static $trm;
	public static $csm;
	public static $ctm;
	public static $rem;
	public static $rlm;

	protected static $runningGaia = FALSE;
	public static $plm;
	public static $sys;
	public static $sem;

	protected static $runningHermes = FALSE;
	public static $ntm;
	public static $rmm;
	public static $cvm;
	public static $cum;
	public static $cme;

	protected static $runningPromethee = FALSE;
	public static $rsm;
	public static $tqm;

	protected static $runningZeus = FALSE;
	public static $pam;
	public static $crt;

	protected static $runningArtemis = FALSE;
	public static $srm;

	protected static $runningDemeter = FALSE;
	public static $tom;
	public static $fmm;
	public static $clm;
	public static $cam;
	public static $elm;
	public static $vom;
	public static $lam;
	public static $vlm;
	public static $fnm;

	protected static $runningAtlas = FALSE;
	public static $prm;
	public static $frm;

	public static function runAres() {
		if (!self::$runningAres) {
			self::$com = new CommanderManager();
			self::$rpm = new ReportManager();
			self::$lrm = new LittleReportManager();
		}
		self::$runningAres = TRUE;
	}

	public static function runAthena() {
		if (!self::$runningAthena) {
			self::$obm = new OrbitalBaseManager();
			self::$crm = new CommercialRouteManager();
			self::$bqm = new BuildingQueueManager();
			self::$sqm = new ShipQueueManager();
			self::$trm = new TransactionManager();
			self::$csm = new CommercialShippingManager();
			self::$ctm = new CommercialTaxManager();
			self::$rem = new RecyclingMissionManager();
			self::$rlm = new RecyclingLogManager();
		}
		self::$runningAthena = TRUE;
	}

	public static function runGaia() {
		if (!self::$runningGaia) {
			self::$plm = new PlaceManager();
			self::$sys = new SystemManager();
			self::$sem = new SectorManager();
		}
		self::$runningGaia = TRUE;
	}

	public static function runHermes() {
		if (!self::$runningHermes) {
			self::$ntm = new NotificationManager();
			self::$rmm = new RoadMapManager();
			self::$cvm = new ConversationManager();
			self::$cum = new ConversationUserManager();
			self::$cme = new ConversationMessageManager();
		}
		self::$runningHermes = TRUE;
	}

	public static function runPromethee() {
		if (!self::$runningPromethee) {
			self::$rsm = new ResearchManager();
			self::$tqm = new TechnologyQueueManager();
		}
		self::$runningPromethee = TRUE;
	}

	public static function runZeus() {
		if (!self::$runningZeus) {
			self::$pam = new PlayerManager();
			self::$crt = new CreditTransactionManager();
		}
		self::$runningZeus = TRUE;
	}

	public static function runArtemis() {
		if (!self::$runningArtemis) {
			self::$srm = new SpyReportManager();
		}
		self::$runningArtemis = TRUE;
	}

	public static function runDemeter() {
		if (!self::$runningDemeter) {
			self::$tom = new ForumTopicManager();
			self::$fmm = new ForumMessageManager();
			self::$clm = new ColorManager();
			self::$cam = new CandidateManager();
			self::$elm = new ElectionManager();
			self::$vom = new VoteManager();
			self::$lam = new LawManager();
			self::$vlm = new VoteLawManager();
			self::$fnm = new FactionNewsManager();
		}
		self::$runningDemeter = TRUE;
	}

	public static function runAtlas() {
		if (!self::$runningAtlas) {
			self::$prm = new PlayerRankingManager();
			self::$frm = new FactionRankingManager();
		}
		self::$runningAtlas = TRUE;
	}

	public static function save() {
		if (self::$runningAres) {
			self::$com->save();
			self::$rpm->save();
			self::$lrm->save();
		}
		if (self::$runningAthena) {
			self::$obm->save();
			self::$crm->save();
			self::$bqm->save();
			self::$sqm->save();
			self::$trm->save();
			self::$csm->save();
			self::$ctm->save();
			self::$rem->save();
			self::$rlm->save();
		}
		if (self::$runningGaia) {
			self::$plm->save();
			self::$sys->save();
			self::$sem->save();
		}
		if (self::$runningHermes) {
			self::$ntm->save();
			self::$cvm->save();
			self::$cum->save();
			self::$cme->save();
		}
		if (self::$runningPromethee) {
			self::$rsm->save();
			self::$tqm->save();
		}
		if (self::$runningZeus) {
			self::$pam->save();
			self::$crt->save();
		}
		if (self::$runningArtemis) {
			self::$srm->save();
		}
		if (self::$runningDemeter) {
			self::$tom->save();
			self::$fmm->save();
			self::$clm->save();
			self::$cam->save();
			self::$elm->save();
			self::$vom->save();
			self::$lam->save();
			self::$vlm->save();
			self::$fnm->save();
		}
		if (self::$runningAtlas) {
			self::$prm->save();
			self::$frm->save();
		}
	}
}
