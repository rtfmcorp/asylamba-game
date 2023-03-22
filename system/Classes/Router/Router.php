<?php

namespace Asylamba\Classes\Router;

use Asylamba\Classes\Library\Http\Request;
use Asylamba\Classes\Library\Http\Response;
use Asylamba\Classes\Library\Session\SessionWrapper;

class Router
{
	private array $pageResources = [
		'profil' => ['profil', 'Profil'],
		'message' => ['message', 'Messagerie'],
		'fleet' => ['fleet', 'Flottes'],
		'financial' => ['financial', 'Finances'],
		'technology' => ['technology', 'Technologie'],
		'spying' => ['spying', 'Espionnage'],

		'diary' => ['diary', 'Journal'],
		'embassy' => ['embassy', 'Ambassades'],

		'bases' => ['bases', 'Vos Bases'],

		'map' => ['map', 'Carte'],

		'faction' => ['faction', 'Votre Faction'],
		'params' => ['params', 'Paramètres'],
		'sponsorship' => ['sponsorship', 'Parrainage'],
		'rank' => ['rank', 'Classements'],

		'admin' => ['admin', 'Administration'],

		'404' => ['notfound', '404'],

		'action' => ['action', 'Action'],
		'ajax' => ['ajax', 'Ajax'],
		'inscription' => ['inscription', 'Inscription'],
		'connection' => ['connection', 'Connexion'],
		'api' => ['api', 'API'],
		'script' => ['script', 'Script'],
		'buffer' => ['buffer', 'Bienvenue']
	];

	public function __construct(
		protected SessionWrapper $sessionWrapper,
		protected string $appRoot,
		protected string $rootPath,
		protected string $getOutRoot,
		protected string $actionPath,
		protected string $ajaxPath,
		protected string $connectionPath,
		protected string $registrationPath,
		protected string $bufferPath,
		protected string $apiPath,
		protected string $templatePath,
		protected string $scriptPath,
		protected string $pagesPath,
		protected string $eventPath,
	) {
	}

	public function processRequest(Request $request): Response
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

	protected function parseRoute(Request $request, Response $response): void
	{
		$requestURI = array_values(array_diff(
			explode('/', $request->getPath()),
			explode('/', $_SERVER['SCRIPT_NAME'])
		));

		if ($this->isResource($requestURI)) {
			$this->serveResource($response, $requestURI);
			return;
		}

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

	protected function isResource(array $requestURI): bool
	{
		$lastUrlPart = $requestURI[\array_key_last($requestURI)];
		$fileParts = explode('.', $lastUrlPart);

		if (2 > \count($fileParts)) {
			return false;
		}
		return \in_array(end($fileParts), ['css', 'js', 'png', 'jpg']) && \is_file($this->getResourcePath($requestURI));
	}

	protected function serveResource(Response $response, array $requestURI): void
	{
		$response->setStatusCode(Response::STATUS_OK);
		$response->setPage('public');
		$response->setBody(\file_get_contents($this->getResourcePath($requestURI)));
	}

	protected function getResourcePath(array $requestURI): string
	{
		return \sprintf('%s%s', $this->rootPath, implode('/', $requestURI));
	}

	public function checkPermission(Request $request, Response $response): void
	{
		$page = $response->getPage();

		if ($page === 'inscription') {
			if ($this->sessionWrapper->exist('playerId')) {
				$response->redirect($this->appRoot);
			}
		} elseif ($page === 'connection') {
			if (!$this->sessionWrapper->exist('playerId')) {
				if (!$request->query->has('bindkey')) {
					$response->redirect($this->getOutRoot . 'accueil/speak-wrongargument');
				}
			} else {
				$response->redirect($this->appRoot);
			}
		} elseif (\in_array($page, array('api', 'script', 'buffer', 'public'))) {
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
				$response->addTemplate($this->actionPath . 'main.php');
				break;
			case 'ajax':
				$response->addTemplate($this->ajaxPath . 'main.php');
				break;
			case 'api':
				$response->addTemplate($this->apiPath . 'main.php');
				break;
			case 'buffer':
				$response->addTemplate($this->bufferPath . 'main.php');
				break;
			case 'script':
				$response->addTemplate($this->scriptPath . 'main.php');
				break;
			case 'connection':
				$response->addTemplate($this->connectionPath . 'main.php');
				break;
			case '404':
                $response->setStatusCode(404);
				$response->addTemplate($this->templatePath . 'notfound.php');
				break;
			case 'inscription':
				$response->addTemplate($this->registrationPath . 'check.php');
				if (!$response->getRedirect()) {
					$response->addTemplate($this->templatePath . $screenMode . '/open.php');
					$response->addTemplate($this->templatePath . $screenMode . '/stepbar.php');
					$response->addTemplate($this->registrationPath . '/content.php');
					$response->addTemplate($this->templatePath . $screenMode . '/btmbar.php');
					$response->addTemplate($this->templatePath . $screenMode . '/alert.php');
					$response->addTemplate($this->templatePath . $screenMode . '/close.php');
				}
				break;
			case 'public': break;
			default:
				$response->addTemplate($this->eventPath . 'loadEvent.php');
				$response->addTemplate($this->templatePath . $screenMode . '/open.php');
				$response->addTemplate($this->templatePath . $screenMode . '/navbar.php');
				$response->addTemplate($this->pagesPath . $screenMode . '/' . $page . '.php');
				$response->addTemplate($this->templatePath . $screenMode . '/toolbar.php');
				$response->addTemplate($this->templatePath . $screenMode . '/alert.php');
				$response->addTemplate($this->templatePath . $screenMode . '/close.php');
				break;
		}
	}
}
