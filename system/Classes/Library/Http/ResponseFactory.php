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
			$response->setBody(\sprintf("%s\n%s", $response->getBody(), \ob_get_clean()));
		} else {
			\ob_end_clean();
		}
    }
    
    protected function createHeaders(Request $request, Response $response): void
    {
		$contentType = ('public' === $response->getPage())
			? $this->guessMimeType($request)
			: (($response instanceof JsonResponse) ? 'application/json' : 'text/html');
		$response->headers->set('Content-Type', $contentType);
        $response->headers->set('Date', gmdate('D, d M Y H:i:s T'));
        $response->headers->set('Status', $response->getStatusCode() . ' ' . $response->getStatus());
		
		if ($response->getStatusCode() === 302) {
			$response->headers->set('Location',
				(!\str_starts_with($response->getRedirect(), 'http'))
				? \sprintf(
					'http://%s%s',
					$request->headers->get('host'),
					(str_starts_with($response->getRedirect(), '/') ? $response->getRedirect() : \sprintf("/%s", $response->getRedirect())),
				)
				: $response->getRedirect()
			);
		}
    }

	protected function guessMimeType(Request $request): string
	{
		$fileParts = \explode('.', $request->getPath());

		return match (\end($fileParts)) {
			'css' => 'text/css',
			'js' => 'text/javascript',
			'png' => 'image/png',
			'jpg' => 'image/jpeg',
		};
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
