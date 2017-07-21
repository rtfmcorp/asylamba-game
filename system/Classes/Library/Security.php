<?php

namespace Asylamba\Classes\Library;

use Asylamba\Classes\Library\Session\SessionWrapper;

class Security {
	/** @var Session **/
	protected $sessionWrapper;
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
		$this->sessionWrapper = $session;
		$this->serverKey = $serverKey;
		$this->iv = $iv;
	}
	
	public function crypt($query, $key = null) {
		$data = urlencode(openssl_encrypt($query,  'AES-128-CBC', ($key !== null) ? $key : $this->serverKey, null, $this->iv));
        $data = rtrim(strtr(base64_encode($data), '+/', '~_'), '=');
		return $data;
	}

	public function uncrypt($cipher) {
		
        $data = base64_decode(str_pad(strtr($cipher, '~_', '+/'), strlen($cipher) % 4, '=', STR_PAD_RIGHT));
		return openssl_decrypt(urldecode($data), 'AES-128-CBC', $this->serverKey, null, $this->iv);
	}

	public function buildBindkey($bindkey) {
		$key  = Utils::generateString(5);
		$key .= '-';
		$key .= $bindkey;
		$key .= '-';
		$key .= time();
		return str_replace('%', '___', $key);
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
