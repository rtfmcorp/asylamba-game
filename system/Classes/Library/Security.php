<?php

namespace Asylamba\Classes\Library;

use Asylamba\Classes\Library\Session\SessionWrapper;

class Security {
	/** @var Session **/
	protected $session;
	/** @var string **/
	protected $serverKey;
	
	/**
	 * @param SessionWrapper $session
	 * @param string $serverKey
	 */
	public function __construct(SessionWrapper $session, $serverKey)
	{
		$this->session = $session;
		$this->serverKey = $serverKey;
	}
	
	public function crypt($query, $key = null) {
		if (!$this->session->exist('security_iv')) {
			$this->session->add('security_iv', openssl_random_pseudo_bytes(16));
		}
		return urlencode(openssl_encrypt($query,  'AES-128-CBC', ($key !== null) ? $key : $this->serverKey, null, $this->session->get('security_iv')));
	}

	public function uncrypt($cipher) {
		if (($iv =  $this->session->get('security_iv')) === null) {
			return false;
		}
		return openssl_decrypt(urldecode($cipher), 'AES-128-CBC', $this->serverKey, null, $iv);
	}

	public function buildBindkey($bindkey) {
		$key  = Utils::generateString(5);
		$key .= '-';
		$key .= $bindkey;
		$key .= '-';
		$key .= time();

		return $key;
	}

	public function extractBindkey($key) {
		$data = explode('-', $key);

		return isset($data[1])
			? $data[1] : FALSE;
	}

	public function extractTime($key) {
		$data = explode('-', $key);

		return isset($data[2])
			? $data[2] : FALSE;
	}
}
