<?php
abstract class CTR {
	public static $data;
	public static $history;
	public static $get;
	public static $post;
	public static $alert;
	public static $cookie;

	private static $page;
	private static $title;
	private static $url;

	private static $redirect;
	private static $xDomain = FALSE;

	private static $lastUpdate;

	public static $benchmark;
	public static $applyGalaxy = FALSE;

	private static $pageResources = array(
		'profil' => array('profil', 'Profil'),
			'message' => array('message', 'Messagerie'),
			'fleet' => array('fleet', 'Flottes'),
			'financial' => array('financial', 'Finances'),
			'technology' => array('technology', 'Technologie'),
			'spying' => array('spying', 'Espionnage'),

		'diary' => array('diary', 'Journal'),

		'bases' => array('bases', 'Vos Bases'),
			'base' => array('base', 'Base'),

		'map' => array('map', 'Carte'),

		'faction' => array('faction', 'Votre Faction'),
		'params' => array('params', 'Paramètres'),
		'rank' => array('rank', 'Classements'),

		'admin' => array('admin', 'Administration'),

		'404' => array('notfound', '404'),
		
		'action' => array('action', 'Action'),
		'ajax' => array('ajax', 'Ajax'),
		'inscription' => array('inscription', 'Inscription'),
		'connection' => array('connection', 'Connexion'),
		'api' => array('api', 'API'),
		'script' => array('script', 'Script')
	);

	public static function getTitle()		{ return self::$title; }
	public static function getPage()		{ return self::$page; }
	public static function getRedirect()	{ return self::$redirect; }
	public static function getLastUpdate()	{ return self::$lastUpdate; }
	public static function getUrl()			{ return self::$url; }

	public static function setLastUpdate()	{ self::$lastUpdate = Utils::now(); }
	public static function redirect($v = 0, $externalDomain = FALSE) {
		self::$xDomain = $externalDomain;
		if ($v === 0) {
			self::$redirect = self::$history->getPastPath();
		} else {
			self::$redirect = $v;
		}
	}

	public static function initialize() {
		self::$benchmark = new Benchmark();

		# initialise les données sessions
		if (isset($_SESSION[SERVER_SESS]['data'])) {
			self::$data = unserialize($_SESSION[SERVER_SESS]['data']);
		} else {
			self::$data = new ArrayList();
		}

		# initialise l'historique sessions
		if (isset($_SESSION[SERVER_SESS]['history'])) {
			self::$history = unserialize($_SESSION[SERVER_SESS]['history']);
		} else {
			self::$history = new History();
		}

		# initialise les données de cookie
		self::$cookie = new Cookie();

		# création des conteneurs à get / POST data
		self::$get  = new ArrayList();
		self::$post = new ArrayList();
		foreach ($_POST as $k => $v) {
			self::$post->add($k, $v);
		}
		self::parseRoute();
		
		# initialise les données de message d'erreur
		if (isset($_SESSION[SERVER_SESS]['alert'])) {
			self::$alert = unserialize($_SESSION[SERVER_SESS]['alert']);
		} else {
			self::$alert = new Alert();
		}

		# initialise le mode de jeu
		if (self::$get->exist('screenmode') AND in_array(self::$get->get('screenmode'), array('desktop', 'mobile'))) {
			self::$data->add('screenmode', self::$get->get('screenmode'));
		} elseif (!self::$data->exist('screenmode')) {
			self::$data->add('screenmode', 'desktop');
		}
	}

	private static function parseRoute() {
		self::$url = $_SERVER['REQUEST_URI'];

		$requestURI = array_diff(
			explode('/', $_SERVER['REQUEST_URI']),
			explode('/', $_SERVER['SCRIPT_NAME'])
		);

		$requestURI = array_values($requestURI);

		$temp = array_keys(self::$pageResources);
		self::$page = (count($requestURI) == 0) ? $temp[0] : $requestURI[0];
		if (in_array(self::$page, array_keys(self::$pageResources))) {
			self::$title = self::$pageResources[self::$page][1];
		} else {
			self::$title = 'Page non trouvée';
			self::$page = '404';
		}

		# rempli l'historique
		if (!in_array(self::$page, array('404', 'action', 'ajax', 'connection', 'api', 'script'))) {
			self::$history->add(implode('/', $requestURI));
		}

		# remplir les paramètres depuis le routing
		for ($i = 1; $i < count($requestURI); $i++) {
			$params = explode('-', $requestURI[$i]);
			if (count($params) == 2) {
				self::$get->add($params[0], $params[1]);
			}
		}
	}

	public static function checkPermission() {
		if (self::$page === 'inscription') {
			if (!self::$data->exist('playerId')) {
				# do nothing
			} else {
				header('Location: ' . APP_ROOT);
				exit();
			}
		} elseif (self::$page === 'connection') {
			if (!self::$data->exist('playerId')) {
				if (!self::$get->exist('bindkey')) {
					header('Location: ' . GETOUT_ROOT . 'accueil/speak-wrongargument');
					exit();
				} else {
					# do nothing
				}
			} else {
				header('Location: ' . APP_ROOT);
				exit();
			}
		} elseif (in_array(self::$page, array('api', 'script'))) {
			# doing nothing
		} else {
			if (!self::$data->exist('playerId')) {
				header('Location: ' . GETOUT_ROOT . 'accueil/speak-loginrequired');
				exit();
			}
		}
	}

	public static function getInclude() {
		if (self::$page == 'action') {
			include ACTION . 'main.php';
		} elseif (self::$page == 'ajax') {
			include AJAX . 'main.php';
		} elseif (self::$page == 'api') {
			include API . 'main.php';
		} elseif (self::$page == 'script') {
			include SCRIPT . 'main.php';
		} elseif (self::$page == 'connection') {
			include CONNECTION . 'main.php';
		} elseif (self::$page == '404') {
			# inclure 404
			header('HTTP/1.0 404 Not Found');
			include TEMPLATE . 'notfound.php';
		} elseif (self::$page == 'inscription') {
			include INSCRIPTION . 'check.php';

			if (empty(self::$redirect)) {
				include TEMPLATE . self::$data->get('screenmode') . '/open.php';
				include TEMPLATE . self::$data->get('screenmode') . '/stepbar.php';

				include INSCRIPTION . 'content.php';
				
				include TEMPLATE . self::$data->get('screenmode') . '/btmbar.php';
				include TEMPLATE . self::$data->get('screenmode') . '/alert.php';
				include TEMPLATE . self::$data->get('screenmode') . '/close.php';
			}
		} else {
			include EVENT . 'loadEvent.php';
			include EVENT . 'executeEvent.php';
			include EVENT . 'updateGame.php';
			
			include TEMPLATE . self::$data->get('screenmode') . '/open.php';
			include TEMPLATE . self::$data->get('screenmode') . '/navbar.php';
			
			include 	PAGES . self::$data->get('screenmode') . '/' . self::$page . '.php';
			
			include TEMPLATE . self::$data->get('screenmode') . '/toolbar.php';
			include TEMPLATE . self::$data->get('screenmode') . '/alert.php';
			include TEMPLATE . self::$data->get('screenmode') . '/close.php';
		}
	}

	public static function getStat() {
		if (self::$page != '404') {
			$path = 'public/log/stats/' . date('Y') . '-' . date('m') . '-' . date('d') . '.log';

			$ctn  = "### " . date('H:i:s') . " ###\r";
			$ctn .= "# path : " . $_SERVER['REQUEST_URI'] . "\r";
			$ctn .= "# time : " . self::$benchmark->getTime('mls', 0) . "ms\r";

			Bug::writeLog($path, $ctn);
		}
	}

	public static function save() {
		# sauvegarde en db des objets
		ASM::save();

		# application de la galaxie si necessaire
		if (CTR::$applyGalaxy) {
			include_once GAIA;
			GalaxyColorManager::applyAndSave();
		}

		# sauvegarde en session des données
		$_SESSION[SERVER_SESS]['data'] = serialize(self::$data);
		$_SESSION[SERVER_SESS]['alert'] = serialize(self::$alert);
		$_SESSION[SERVER_SESS]['history'] = serialize(self::$history);

		# fin du benchmark
		self::getStat();

		# redirection, si spécifié
		if (!empty(self::$redirect)) {
			if (self::$xDomain == TRUE) {
				header('Location: ' . self::$redirect);
			} else {
				header('Location: ' . APP_ROOT . self::$redirect);
			}
			exit();
		}
	}
}
?>