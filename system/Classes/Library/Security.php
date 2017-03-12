<?php

namespace Asylamba\Classes\Library;

class Security {
	public function crypt($query, $key) {
		$iv = openssl_random_pseudo_bytes(16);

		$cipher = openssl_encrypt($query,  'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);

		return rtrim(strtr(base64_encode($cipher), '+/', '~_'), '=');
	}

	public function uncrypt($query, $key) {
		$iv = openssl_random_pseudo_bytes(16);
		
		$cipher = base64_decode(str_pad(strtr($query, '~_', '+/'), strlen($query) % 4, '=', STR_PAD_RIGHT));
		
		return trim(openssl_decrypt($cipher, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv));
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
