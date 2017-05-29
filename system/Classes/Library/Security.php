<?php

namespace Asylamba\Classes\Library;

use Asylamba\Classes\Library\Session\SessionWrapper;

class Security {
	/** @var Session **/
	protected $session;
	/** @var string **/
	protected $serverKey;
	/** @var string **/
	protected $iv;
	
	/**
	 * @param SessionWrapper $session
	 * @param string $serverKey
	 * @param string $iv
	 */
	public function __construct(SessionWrapper $session, $serverKey, $iv)
	{
		$this->session = $session;
		$this->serverKey = $serverKey;
		$this->iv = $iv;
	}
	
	public function crypt($query, $key = null) {
		return urlencode(openssl_encrypt($query,  'AES-128-CBC', ($key !== null) ? $key : $this->serverKey, null, $this->iv));
	}

	public function uncrypt($cipher) {
		return openssl_decrypt(urldecode($cipher), 'AES-128-CBC', $this->serverKey, null, $this->iv);
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
