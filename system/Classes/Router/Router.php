<?php

namespace Asylamba\Classes\Router;

use Asylamba\Classes\Library\Http\Request;
use Asylamba\Classes\Library\Http\Response;
use Asylamba\Classes\Library\Session\SessionWrapper;

class Router
{
	/** @var SessionWrapper **/
	protected $sessionWrapper;
	/** @var string **/
	protected $getOutRoot;
	
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
		'budget' => array('budget', 'Financement'),
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
	
	/**
	 * @param SessionWrapper $session
	 * @param string $getOutRoot
	 */
	public function __construct(SessionWrapper $session, $getOutRoot)
	{
		$this->sessionWrapper = $session;
		$this->getOutRoot = $getOutRoot;
	}
	
	/**
	 * @param Request $request
	 * @param Client $client
	 * @return Response
	 */
	public function processRequest(Request $request)
	{
		$response = new Response();
		$this->parseRoute($request, $response);
		$this->checkPermission($request, $response);
		
		if (!empty($response->getRedirect())) {
			return $response;
		}
		
		$this->getInclude($response);
		return $response;
	}

	/**
	 * @param Request $request
	 * @param Response $response
	 */
	protected function parseRoute(Request $request, Response $response) {
		$requestURI = array_values(array_diff(
			explode('/', $request->getPath()),
			explode('/', $_SERVER['SCRIPT_NAME'])
		));

		$temp = array_keys($this->pageResources);
		$page = (empty($requestURI)) ? $temp[0] : ((empty($requestURI[0])) ? $requestURI[1] : $requestURI[0]);
		
		if (in_array($page, array_keys($this->pageResources))) {
			$response->setTitle($this->pageResources[$page][1]);
		} else {
			$response->setTitle('Page non trouvée');
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
			$this->sessionWrapper->addHistory($newURI);
		}

		$nbParams = count($requestURI);
		// remplir les paramètres depuis le routing
		for ($i = 1; $i < $nbParams; ++$i) {
			$param = explode('-', $requestURI[$i]);
			if (count($param) === 2) {
				$request->query->set($param[0], $param[1]);
			}
		}
		$this->sessionWrapper->add('screenmode',
			(($screenMode = $request->query->get('screenmode')) && in_array($screenMode, ['desktop', 'mobile']))
			? $screenMode
			: 'desktop'
		);
	}

	/**
	 * @param Request $request
	 * @param Response $response
	 */
	public function checkPermission(Request $request, Response $response) {
		$page = $response->getPage();
        
		if ($page === 'inscription') {
			if ($this->sessionWrapper->exist('playerId')) {
				$response->redirect(APP_ROOT);
			}
		} elseif ($page === 'connection') {
			if (!$this->sessionWrapper->exist('playerId')) {
				if (!$request->query->has('bindkey')) {
					$response->redirect($this->getOutRoot . 'accueil/speak-wrongargument');
				}
			} else {
				$response->redirect(APP_ROOT);
			}
		} elseif (in_array($page, array('api', 'script', 'buffer'))) {
			# doing nothing
		} else {
			if (!$this->sessionWrapper->exist('playerId')) {
				$response->redirect($this->getOutRoot . 'accueil/speak-loginrequired');
			}
		}
	}

	public function getInclude(Response $response) {
		$page = $response->getPage();
		$screenMode = $this->sessionWrapper->get('screenmode');
		switch($page) {
			case 'action':
				$response->addTemplate(ACTION . 'main.php');
				break;
			case 'ajax':
				$response->addTemplate(AJAX . 'main.php');
				break;
			case 'api':
				$response->addTemplate(API . 'main.php');
				break;
			case 'buffer':
				$response->addTemplate(BUFFER . 'main.php');
				break;
			case 'script':
				$response->addTemplate(SCRIPT . 'main.php');
				break;
			case 'connection':
				$response->addTemplate(CONNECTION . 'main.php');
				break;
			case '404':
                $response->setStatusCode(404);
				$response->addTemplate(TEMPLATE . 'notfound.php');
				break;
			case 'inscription':
				$response->addTemplate(INSCRIPTION . 'check.php');
				if (!$response->getRedirect()) {
					$response->addTemplate(TEMPLATE . $screenMode . '/open.php');
					$response->addTemplate(TEMPLATE . $screenMode . '/stepbar.php');
					$response->addTemplate(INSCRIPTION . '/content.php');
					$response->addTemplate(TEMPLATE . $screenMode . '/btmbar.php');
					$response->addTemplate(TEMPLATE . $screenMode . '/alert.php');
					$response->addTemplate(TEMPLATE . $screenMode . '/close.php');
				}
				break;
			default:
				$response->addTemplate(EVENT . 'loadEvent.php');
				$response->addTemplate(TEMPLATE . $screenMode . '/open.php');
				$response->addTemplate(TEMPLATE . $screenMode . '/navbar.php');
				$response->addTemplate(PAGES . $screenMode . '/' . $page . '.php');
				$response->addTemplate(TEMPLATE . $screenMode . '/toolbar.php');
				$response->addTemplate(TEMPLATE . $screenMode . '/alert.php');
				$response->addTemplate(TEMPLATE . $screenMode . '/close.php');
				break;
		}
	}
}