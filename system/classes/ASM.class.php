<?php
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
	public static $msm;
	public static $mrm;
	public static $rmm;

	protected static $runningPromethee = FALSE;
	public static $rsm;
	public static $tqm;

	protected static $runningZeus = FALSE;
	public static $pam;
	public static $crt;

	protected static $runningArtemis = FALSE;
	public static $srm;

	protected static $runningApollon = FALSE;
	public static $btm;

	protected static $runningDemeter = FALSE;
	public static $tom;
	public static $fmm;
	public static $clm;
	public static $cam;
	public static $elm;
	public static $vom;
	public static $lam;
	public static $vlm;

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
			self::$msm = new MessageManager();
			self::$mrm = new MessageRadioManager();
			self::$rmm = new RoadMapManager();
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

	public static function runApollon() {
		if (!self::$runningApollon) {
			self::$btm = new BugTrackerManager();
		}
		self::$runningApollon = TRUE;
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
			self::$msm->save();
			self::$mrm->save();
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
		if (self::$runningApollon) {
			self::$btm->save();
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
		}
		if (self::$runningAtlas) {
			self::$prm->save();
			self::$frm->save();
		}
	}
}