<?php

namespace Asylamba\Classes\Library\Http;

use Asylamba\Classes\Templating\Renderer;

use Asylamba\Classes\Daemon\Client;

class ResponseFactory
{
    protected Renderer $templating;
	protected int $sessionLifetime;
    
    public function __construct(Renderer $renderer)
    {
        $this->templating = $renderer;
		$this->sessionLifetime = ini_get("session.gc_maxlifetime");
    }
    
    public function processResponse(Request $request, Response $response, Client $client): void
    {
        $response->setProtocol($request->getProtocol());
        $this->templating->render($response);
        $this->createHeaders($request, $response);
		$this->createCookies($request, $response, $client);
		if ($response->getStatusCode() !== 302) {
			$response->setBody(ob_get_clean());
		} else {
			ob_end_clean();
		}
    }
    
    protected function createHeaders(Request $request, Response $response): void
    {
        $response->headers->set('Content-Type', ($response instanceof JsonResponse) ? 'application/json' : 'text/html');
        $response->headers->set('Date', gmdate('D, d M Y H:i:s T'));
        $response->headers->set('Status', $response->getStatusCode() . ' ' . $response->getStatus());
		
		if ($response->getStatusCode() === 302) {
			$response->headers->set('Location',
				(substr($response->getRedirect(), 0, 4) !== 'http')
				? 'http://' . $request->headers->get('host') . '/' . $response->getRedirect()
				: $response->getRedirect()
			);
		}
    }

	protected function createCookies(Request $request, Response $response, Client $client): void
	{
		$cookies = [];
		if (!$request->cookies->exist('session_id') || $request->cookies->get('session_id') !== $client->getId()) {
			$cookies[] = 'session_id=' . $client->getId() . '; path=/; expires=' . gmdate('D, d M Y H:i:s T', time() + $this->sessionLifetime) . '; HttpOnly;';
		}
		foreach ($request->cookies->getNewElements() as $key => $value) {
			$cookies[] = $key . '=' . $value . '; path=/; expires=' . gmdate('D, d M Y H:i:s T', time() + 3000000000) . '; HttpOnly;';
		}
		if (!empty($cookies)) {
			$response->headers->set('Set-Cookie', implode(',', $cookies));
		}
	}
}
