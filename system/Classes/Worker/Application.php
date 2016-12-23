<?php

namespace Asylamba\Classes\Worker;

use Asylamba\Classes\Configuration\Configuration;
use Symfony\Component\Config\FileLocator;

use Asylamba\Classes\Library\Benchmark;
use Asylamba\Classes\Container\History;
use Asylamba\Classes\Container\Session;
use Asylamba\Classes\Container\Alert;
use Asylamba\Classes\Library\Bug;

use Asylamba\Classes\Library\Http\Request;
use Asylamba\Classes\Library\Http\Response;

use Asylamba\Classes\Event\ExceptionEvent;
use Asylamba\Classes\Event\ErrorEvent;

use Asylamba\Classes\DependencyInjection\Container;

class Application {
    /** @var Container **/
    protected $container;
	/** @var array **/
	protected $modules;
    
    public function boot()
    {
		try {
			$this->container = new Container();
			$this->container->set('app.container', $this->container);
			$this->configure();
			$this->registerModules();
			$this->init();
			$this->checkPermission();
			$this->getInclude();
			$this->save();
		} catch (\Exception $ex) {
			$this->container->get('event_dispatcher')->dispatch(new ExceptionEvent($ex));
			die("{$ex->getMessage()} in {$ex->getFile()} at {$ex->getLine()}<br><pre>" . $ex->getTraceAsString() . '</pre>');
		} catch (\Error $err) {
			$this->container->get('event_dispatcher')->dispatch(new ErrorEvent($err));
			die("{$err->getMessage()} in {$err->getFile()} at {$err->getLine()}<br><pre>" . $err->getTraceAsString() . '</pre>');
		}
    }
	
	public function configure()
	{
		$configurationFiles = [
			__DIR__ . '/../../../config/parameters.yml',
			__DIR__ . '/../../../config/services.yml'
		];
		$configuration = new Configuration(new FileLocator($configurationFiles));
		$configuration->buildContainer($this->container, $configurationFiles);
		
		$this->container->setParameter('root_path', realpath('.'));
	}
	
	public function registerModules()
	{
		$this->modules = [
			'ares' => new \Asylamba\Modules\Ares\AresModule($this),
			'artemis' => new \Asylamba\Modules\Artemis\ArtemisModule($this),
			'athena' => new \Asylamba\Modules\Athena\AthenaModule($this),
			'atlas' => new \Asylamba\Modules\Atlas\AtlasModule($this),
			'demeter' => new \Asylamba\Modules\Demeter\DemeterModule($this),
			'gaia' => new \Asylamba\Modules\Gaia\GaiaModule($this),
			'hermes' => new \Asylamba\Modules\Hermes\HermesModule($this),
			'promethee' => new \Asylamba\Modules\Promethee\PrometheeModule($this),
			'zeus' => new \Asylamba\Modules\Zeus\ZeusModule($this)
		];
	}
    
    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }
	
	/**
	 * @return array
	 */
	public function getModules()
	{
		return $this->modules;
	}
	
	/**
	 * @param string $name
	 * @return \Asylamba\Classes\Library\Module
	 */
	public function getModule($name)
	{
		return $this->modules[$name];
	}

	private $pageResources = array(
		'profil' => array('profil', 'Profil'),
		'message' => array('message', 'Messagerie'),
		'fleet' => array('fleet', 'Flottes'),
		'financial' => array('financial', 'Finances'),
		'technology' => array('technology', 'Technologie'),
		'spying' => array('spying', 'Espionnage'),

		'diary' => array('diary', 'Journal'),
		'embassy' => array('embassy', 'Ambassades'),

		'bases' => array('bases', 'Vos Bases'),

		'map' => array('map', 'Carte'),

		'faction' => array('faction', 'Votre Faction'),
		'params' => array('params', 'Paramètres'),
		'sponsorship' => array('sponsorship', 'Parrainage'),
		'rank' => array('rank', 'Classements'),

		'admin' => array('admin', 'Administration'),

		'404' => array('notfound', '404'),

		'action' => array('action', 'Action'),
		'ajax' => array('ajax', 'Ajax'),
		'inscription' => array('inscription', 'Inscription'),
		'connection' => array('connection', 'Connexion'),
		'api' => array('api', 'API'),
		'script' => array('script', 'Script'),
		'buffer' => array('buffer', 'Bienvenue')
	);
	
	public function init() {
		$this->container->set('app.benchmark', new Benchmark());
		$this->container->set('app.session', 
			(isset($_SESSION[SERVER_SESS]['data']))
			? unserialize($_SESSION[SERVER_SESS]['data'])
			: new Session()
		);
		$this->container->set('app.history',
			(isset($_SESSION[SERVER_SESS]['history']))
			? unserialize($_SESSION[SERVER_SESS]['history'])
			: new History()
		);
		
		if ($this->getContainer()->getParameter('environment') === 'dev') {
			set_error_handler(function($errno, $errstr, $errfile, $errline) {
				throw new \ErrorException($errstr, $errno, 1, $errfile, $errline);
			});
		}
		
		$request = new Request();
		$request->initialize();
		$this->container->set('app.request', $request);
		
		self::parseRoute();

		$this->container->set('app.alert', 
			(isset($_SESSION[SERVER_SESS]['alert']))
			? unserialize($_SESSION[SERVER_SESS]['alert'])
			: new Alert()
		);
		$this->container->get('app.session')->add('screenmode',
			(($screenMode = $request->query->get('screenmode')) && in_array($screenMode, ['desktop', 'mobile']))
			? $screenMode
			: 'desktop'
		);
	}

	private function parseRoute() {
		$request = $this->container->get('app.request');
		$request->setUrl($_SERVER['REQUEST_URI']);
		
		$response = new Response($request, $this->container->get('app.history'));
		$this->container->set('app.response', $response);

		$requestURI = array_values(array_diff(
			explode('/', $_SERVER['REQUEST_URI']),
			explode('/', $_SERVER['SCRIPT_NAME'])
		));

		$temp = array_keys($this->pageResources);
		$page = (count($requestURI) == 0) ? $temp[0] : $requestURI[0];
		if (in_array($page, array_keys($this->pageResources))) {
			$this->title = $this->pageResources[$page][1];
		} else {
			$this->title = 'Page non trouvée';
			$page = '404';
		}
		$response->setPage($page);
		// Fill the history
		if (!in_array($page, array('404', 'action', 'ajax', 'connection', 'api', 'script'))) {
			$newURI = 
				(implode('/', $requestURI) == '')
				? 'profil'
				: implode('/', $requestURI)
			;
			$this->container->get('app.history')->add($newURI);
		}

		$nbParams = count($requestURI);
		// remplir les paramètres depuis le routing
		for ($i = 1; $i < $nbParams; ++$i) {
			$param = explode('-', $requestURI[$i]);
			if (count($param) === 2) {
				$request->query->set($param[0], $param[1]);
			}
		}
	}

	public function checkPermission() {
		$page = $this->container->get('app.response')->getPage();
		$session = $this->container->get('app.session');
		
		if ($page === 'inscription') {
			if (!$session->exist('playerId')) {
				# do nothing
			} else {
				header('Location: ' . APP_ROOT);
				exit();
			}
		} elseif ($page === 'connection') {
			if (!$session->exist('playerId')) {
				if (!$this->container->get('app.request')->query->has('bindkey')) {
					header('Location: ' . GETOUT_ROOT . 'accueil/speak-wrongargument');
					exit();
				} else {
					# do nothing
				}
			} else {
				header('Location: ' . APP_ROOT);
				exit();
			}
		} elseif (in_array($page, array('api', 'script', 'buffer'))) {
			# doing nothing
		} else {
			if (!$session->exist('playerId')) {
				header('Location: ' . GETOUT_ROOT . 'accueil/speak-loginrequired');
				exit();
			}
		}
	}

	public function getInclude() {
		ob_start();
		$page = $this->container->get('app.response')->getPage();
		$screenMode = $this->container->get('app.session')->get('screenmode');
		
		switch($page) {
			case 'action':
				include ACTION . 'main.php';
				break;
			case 'ajax':
				include AJAX . 'main.php';
				break;
			case 'api':
				include API . 'main.php';
				break;
			case 'buffer':
				include BUFFER . 'main.php';
				break;
			case 'script':
				include SCRIPT . 'main.php';
				break;
			case 'connection':
				include CONNECTION . 'main.php';
				break;
			case '404':
				header('HTTP/1.0 404 Not Found');
				include TEMPLATE . 'notfound.php';
				break;
			case 'inscription':
				include INSCRIPTION . 'check.php';
				if (!$this->container->get('app.response')->getRedirect()) {
					include TEMPLATE . $screenMode . '/open.php';
					include TEMPLATE . $screenMode . '/stepbar.php';
					include INSCRIPTION . 'content.php';
					include TEMPLATE . $screenMode . '/btmbar.php';
					include TEMPLATE . $screenMode . '/alert.php';
					include TEMPLATE . $screenMode . '/close.php';
				}
				break;
			default:
				include EVENT . 'loadEvent.php';
				include EVENT . 'executeEvent.php';
				include EVENT . 'updateGame.php';
				include TEMPLATE . $screenMode . '/open.php';
				include TEMPLATE . $screenMode . '/navbar.php';
				include PAGES . $screenMode . '/' . $page . '.php';
				include TEMPLATE . $screenMode . '/toolbar.php';
				include TEMPLATE . $screenMode . '/alert.php';
				include TEMPLATE . $screenMode . '/close.php';
				break;
		}
	}

	public function getStat() {
		if ($this->container->get('app.response')->getPage() != '404') {
			$path = 'public/log/stats/' . date('Y') . '-' . date('m') . '-' . date('d') . '.log';

			$ctn  = "### " . date('H:i:s') . " ###\r";
			$ctn .= "# path  : " . $_SERVER['REQUEST_URI'] . "\r";
			$ctn .= "# time  : " . $this->container->get('app.benchmark')->getTime('mls', 0) . "ms\r";
			$ctn .= "# query : " . $this->container->get('database')->getNbrOfQuery() . "\r";

			Bug::writeLog($path, $ctn);
		}
	}

	public function save() {
		# sauvegarde en db des objets
		$this->container->save();
		# application de la galaxie si necessaire
		$galaxyColorManager = $this->container->get('gaia.galaxy_color_manager');
		if ($galaxyColorManager->mustApply()) {
			$galaxyColorManager->applyAndSave();
		}

		# sauvegarde en session des données
		$_SESSION[SERVER_SESS]['data'] = serialize($this->container->get('app.session'));
		$_SESSION[SERVER_SESS]['alert'] = serialize($this->container->get('app.alert'));
		$_SESSION[SERVER_SESS]['history'] = serialize($this->container->get('app.history'));

		# fin du benchmark
		$this->getStat();

		$response = $this->container->get('app.response');
		# redirection, si spécifié
		if (($redirect = $response->getRedirect())) {
			if ($this->container->get('app.request')->getCrossDomain() == TRUE) {
				header('Location: /' . $redirect);
			} else {
				header('Location: ' . APP_ROOT . $redirect);
			}
			exit();
		} else {
			ob_end_flush();
		}
	}
}