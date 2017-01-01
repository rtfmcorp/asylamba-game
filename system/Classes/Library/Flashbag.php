<?php

namespace Asylamba\Classes\Library;

class Flashbag {
	/** @var string **/
	protected $message;
	/** @var string **/
	protected $type;
	
	const TYPE_ERROR = 200;
	# alert constantes
	const TYPE_DEFAULT = 0;

	const TYPE_STD_INFO =		100;
	const TYPE_STD_ERROR =		101;
	const TYPE_SUCCESS =		102;
	const TYPE_STD_FILLFORM =	103;	# error in form filling

	const TYPE_BUG_INFO =		200;
	const TYPE_BUG_ERROR =		201;
	const TYPE_BUG_SUCCESS =		202;

	const TYPE_GENERATOR_SUCCESS = 300;	# building construction
	const TYPE_REFINERY_SUCCESS = 301;	# refinery : silo full
	const TYPE_TECHNOLOGY_SUCCESS =	302;	# new techno
	const TYPE_DOCK1_SUCCESS = 303;	# ship construction
	const TYPE_DOCK2_SUCCESS = 304;	# ship construction
	const TYPE_DOCK3_SUCCESS = 305;	# mothership construction
	const TYPE_MARKET_SUCCESS = 306;	# new route

	const TYPE_GAM_NOMORECASH =	307;
	const TYPE_GAM_RESEARCH =	308;	# reseach found
	const TYPE_GAM_NOTIF =		309;	# new notif
	const TYPE_GAM_MESSAGE =		310;	# new message
	const TYPE_GAM_SPY =			311;	# somebody is attacking you
	const TYPE_GAM_ATTACK =		312;	# fight
	const TYPE_GAM_MARKET =		313;	# transaction in the market

	/**
	 * @param string $message
	 * @param string $type
	 */
	public function __construct($message, $type)
	{
		$this->message = $message;
		$this->type = $type;
	}
	
	/**
	 * @return string
	 */
	public function getMessage()
	{
		return $this->message;
	}
	
	/**
	 * @return string
	 */
	public function getType()
	{
		return $this->type;
	}
}