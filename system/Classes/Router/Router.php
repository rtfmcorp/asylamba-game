<?php

namespace Asylamba\Classes\Router;

use Asylamba\Classes\Library\Http\Request;
use Asylamba\Classes\Library\Http\Response;

use Asylamba\Classes\Container\Session;
use Asylamba\Classes\Daemon\Client;

class Router
{
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
	
	/**
	 * @param Request $request
	 * @param Client $client
	 * @return Response
	 */
	public function processRequest(Request $request, Client $client)
	{
		$response = new Response();
        $session = $client->getSession();
		$this->parseRoute($request, $response, $session);
		$this->checkPermission($request, $response, $session);
		
		if (!empty($response->getRedirect())) {
			return $response;
		}
		
		$this->getInclude($response, $session);
		return $response;
	}

	/**
	 * @param Request $request
	 * @param Response $response
	 * @param Session $session
	 */
	protected function parseRoute(Request $request, Response $response, Session $session) {
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
			$session->addHistory($newURI);
		}

		$nbParams = count($requestURI);
		// remplir les paramètres depuis le routing
		for ($i = 1; $i < $nbParams; ++$i) {
			$param = explode('-', $requestURI[$i]);
			if (count($param) === 2) {
				$request->query->set($param[0], $param[1]);
			}
		}
		$session->add('screenmode',
			(($screenMode = $request->query->get('screenmode')) && in_array($screenMode, ['desktop', 'mobile']))
			? $screenMode
			: 'desktop'
		);
	}

	/**
	 * @param Request $request
	 * @param Response $response
	 * @param Session $session
	 */
	public function checkPermission(Request $request, Response $response, Session $session) {
		$page = $response->getPage();
        
		if ($page === 'inscription') {
			if ($session->exist('playerId')) {
				$response->redirect(APP_ROOT);
			}
		} elseif ($page === 'connection') {
			if (!$session->exist('playerId')) {
				if (!$request->query->has('bindkey')) {
					$response->redirect(GETOUT_ROOT . 'accueil/speak-wrongargument');
				}
			} else {
				$response->redirect(APP_ROOT);
			}
		} elseif (in_array($page, array('api', 'script', 'buffer'))) {
			# doing nothing
		} else {
			if (!$session->exist('playerId')) {
				$response->redirect(GETOUT_ROOT . 'accueil/speak-loginrequired');
			}
		}
	}

	public function getInclude(Response $response, Session $session) {
		$page = $response->getPage();
		$screenMode = $session->get('screenmode');
		
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
				$response->addTemplate(EVENT . 'executeEvent.php');
				$response->addTemplate(EVENT . 'updateGame.php');
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