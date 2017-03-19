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

use Asylamba\Classes\Event\ExceptionEvent;
use Asylamba\Classes\Event\ErrorEvent;

use Asylamba\Classes\DependencyInjection\Container;

class Application implements ApplicationInterface {
    /** @var Container **/
    protected $container;
	/** @var array **/
	protected $modules;
    
    public function boot()
    {
		ob_start();
		$errorEvent = null;
		try {
			$this->container = new Container();
			$this->container->set('app.container', $this->container);
			$this->configure();
			$this->registerModules();
			$this->init();
			$this->render();
			$this->save();
		} catch (\Exception $ex) {
			$errorEvent = new ExceptionEvent($ex);
		} catch (\Error $err) {
			$errorEvent = new ErrorEvent($err);
		}
		if ($errorEvent !== null) {
			$this->container->get('event_dispatcher')->dispatch($errorEvent);
			$pastPath = $this->container->get('app.history')->getPastPath(1);
			if (ob_get_level() !== 0) {
				ob_end_clean();
			}
			header('Location: /' . (($pastPath !== null) ? $pastPath : ''));
		}
    }
	
	public function configure()
	{
		$configurationFiles = [
			__DIR__ . '/../../../config/parameters.yml',
			__DIR__ . '/../../../config/services.yml'
		];
		$this->container->setParameter('root_path', realpath('.'));
		$configuration = new Configuration(new FileLocator($configurationFiles));
		$configuration->buildContainer($this->container, $configurationFiles);
		$configuration->defineOldConstants();
		
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
	
	public function init() {
		$this->container->set('app.benchmark', new Benchmark());
		$session =
			(isset($_SESSION[SERVER_SESS]['data']))
			? unserialize($_SESSION[SERVER_SESS]['data'])
			: new Session()
		;
		$this->container->set('app.session', $session);
		$session->initFlashbags();
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
		
		$this->container->set('app.response',
			$this->container->get('router')->processRequest($request, $session)
		);

		$this->container->set('app.alert', 
			(isset($_SESSION[SERVER_SESS]['alert']))
			? unserialize($_SESSION[SERVER_SESS]['alert'])
			: new Alert()
		);
		$session->add('screenmode',
			(($screenMode = $request->query->get('screenmode')) && in_array($screenMode, ['desktop', 'mobile']))
			? $screenMode
			: 'desktop'
		);
		$this->container->get('entity_manager')->init();
		
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
	
	public function render()
	{
		$response = $this->container->get('app.response');
		# redirection, si spécifié
		if (($redirect = $response->getRedirect())) {
			header('Location: ' . $redirect);
			exit();
		} else {
			$this->container->get('templating.renderer')->render($response);
			ob_end_flush();
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

		$this->container->get('app.session')->saveFlashbags();
		# fin du benchmark
		$this->getStat();
	}
}