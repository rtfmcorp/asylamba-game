<?php

namespace Asylamba\Classes\Library\Http;

use Asylamba\Classes\Templating\Renderer;

use Asylamba\Classes\Daemon\Client;

class ResponseFactory
{
    /** @var Renderer **/
    protected $templating;
	/** @var int **/
	protected $sessionLifetime;
    
    /**
     * @param Renderer $renderer
     */
    public function __construct(Renderer $renderer)
    {
        $this->templating = $renderer;
		$this->sessionLifetime = ini_get("session.gc_maxlifetime");
    }
    
    /**
     * @param \Asylamba\Classes\Library\Http\Request $request
     * @param \Asylamba\Classes\Library\Http\Response $response
	 * @param Client $client
     */
    public function processResponse(Request $request, Response $response, Client $client)
    {
        $response->setProtocol($request->getProtocol());
        $this->templating->render($response);
        $this->createHeaders($request, $response);
		$this->createCookies($request, $response, $client);
		if ($response->getStatusCode() !== 302) {
			$response->setBody(ob_get_clean());
		} else {
			ob_get_clean();
		}
    }
    
    /**
	 * @param Request $request
     * @param \Asylamba\Classes\Library\Http\Response $response
     */
    protected function createHeaders(Request $request, Response $response)
    {
        $response->headers->set('Content-Type', ($response instanceof JsonResponse) ? 'application/json' : 'text/html');
        $response->headers->set('Date', gmdate('D, d M Y H:i:s T'));
        $response->headers->set('Status', $response->getStatusCode() . ' ' . $response->getStatus());
		
		if (($redirect = $response->getRedirect()) !== null) {
			$response->headers->set('Location', 'http://' . $request->headers->get('host') . '/' . $redirect);
		}
    }
	
	/**
	 * @param \Asylamba\Classes\Library\Http\Request $request
	 * @param \Asylamba\Classes\Library\Http\Response $response
	 * @param Client $client
	 */
	protected function createCookies(Request $request, Response $response, Client $client)
	{
		if (!$request->cookies->exist('session_id') || $request->cookies->get('session_id') !== $client->getId()) {
			$response->headers->set('Set-Cookie', 'session_id=' . $client->getId() . '; path=/; expires=' . gmdate('D, d M Y H:i:s T', time() + $this->sessionLifetime) . '; HttpOnly');
		}
	}
}