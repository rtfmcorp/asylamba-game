<?php

namespace Asylamba\Classes\Library;

use Asylamba\Classes\Library\Session\SessionWrapper;

class Security
{
	const CYPHER_ALGO = 'AES-128-CBC';

	public function __construct(
		protected SessionWrapper $sessionWrapper,
		protected string $serverKey,
		protected string $iv,
	) {
	}
	
	public function crypt(string $query, string $key = null): string
	{
		$data = urlencode(openssl_encrypt(
			$query,
			self::CYPHER_ALGO,
			($key !== null) ? $key : $this->serverKey,
			0,
			$this->iv
		));

        return rtrim(strtr(base64_encode($data), '+/', '~_'), '=');
	}

	public function uncrypt(string $cipher): string|false
	{
        $data = base64_decode(str_pad(strtr($cipher, '~_', '+/'), strlen($cipher) % 4, '=', STR_PAD_RIGHT));
		
		return openssl_decrypt(urldecode($data), self::CYPHER_ALGO, $this->serverKey, 0, $this->iv);
	}

	public function buildBindkey(string $bindKey): string
	{
		return str_replace(
			'%',
			'___',
			\sprintf('%s-%s-%s', Utils::generateString(5), $bindKey, time())
		);
	}

	public function extractBindKey(string $key): ?string
	{
		$data = explode('-', $key);

		return $data[1] ?? null;
	}

	public function extractTime(string $key): ?string
	{
		$data = explode('-', $key);

		return $data[2] ?? null;
	}
}
