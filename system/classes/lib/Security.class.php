<?php
class Security {
	public static function crypt($query, $key) {
		$cipher = mcrypt_module_open('rijndael-256', '', 'ecb', '');
		$ivSize = mcrypt_enc_get_iv_size($cipher);
		$iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);

		mcrypt_generic_init($cipher, $key, $iv);

		$data = mcrypt_generic($cipher, $query); 
		$data = rtrim(strtr(base64_encode($data), '+/', '|_'), '=');

		mcrypt_generic_deinit($cipher);

		return $data;
	}

	public static function uncrypt($query, $key) {
		$cipher = mcrypt_module_open('rijndael-256', '', 'ecb', '');
		$ivSize = mcrypt_enc_get_iv_size($cipher);
		$iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);

		mcrypt_generic_init($cipher, $key, $iv);

		$crypted = base64_decode(str_pad(strtr($query, '|_', '+/'), strlen($query) % 4, '=', STR_PAD_RIGHT));
		$decrypted = trim(mdecrypt_generic($cipher, $crypted));

		mcrypt_generic_deinit($cipher);
		mcrypt_module_close($cipher);  

		return $decrypted;
	}

	public static function buildBindkey($bindkey) {
		$key  = Utils::generateString(5);
		$key .= '-';
		$key .= $bindkey;
		$key .= '-';
		$key .= time();

		return $key;
	}

	public static function extractBindkey($key) {
		$data = explode('-', $key);

		return isset($data[1])
			? $data[1] : FALSE;
	}

	public static function extractTime($key) {
		$data = explode('-', $key);

		return isset($data[2])
			? $data[2] : FALSE;
	}
}