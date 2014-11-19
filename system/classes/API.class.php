<?php
class API {
	# API user
	private $path;

	public $query;
	public $data;

	const TEMPLATE_INACTIVE_PLAYER = 51;

	public function __construct($path) {
		$this->path = $path;
	}

	private function query($api, $args) {
		$arg = '';
		$ch  = curl_init();

		foreach ($args as $k => $v) { $arg .= $k . '-' . $v . '/'; }
		$this->query = $this->path . API::parse('a-' . $api . '/' . $arg);

		curl_setopt($ch, CURLOPT_URL, $this->query);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , TRUE);
		$answer = curl_exec($ch);
		curl_close($ch);

		if ($answer !== FALSE) {
			$this->data = unserialize($answer);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function userExist($bindkey) {
		if ($this->query('userexist', array('bindkey' => $bindkey))) {
			if ($this->data['statement'] == 'success') {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	public function confirmInscription($bindkey, $serverId) {
		if ($this->query('confirminscription', array('bindkey' => $bindkey, 'serverid' => $serverId))) {
			if ($this->data['statement'] == 'success') {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	public function confirmConnection($bindkey, $serverId) {
		if ($this->query('confirmconnection', array('bindkey' => $bindkey, 'serverid' => $serverId))) {
			if ($this->data['statement'] == 'success') {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	public function playerIsDead($bindkey, $serverId) {
		if ($this->query('playerisdead', array('bindkey' => $bindkey, 'serverid' => $serverId))) {
			if ($this->data['statement'] == 'success') {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	public function sendMail($bindkey, $serverId, $template) {
		if ($this->query('sendmail', array('bindkey' => $bindkey, 'serverid' => $serverId, 'template' => $template))) {
			if ($this->data['statement'] == 'success') {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	public function getPlayerStatement($bindkey) {
		# a faire
		return FALSE;
		return $this->query('getplayerstatement', array('bindkey' => $bindkey));
	}

	public function banPlayer($bindkey) {
		# a faire
		return FALSE;
		if ($this->query('ban', array('bindkey' => $bindkey))) {
			if ($this->data['statement'] == 'success') {
				return 'ok';
			} else {
				return $this->data['message'];
			}
		} else {
			return 'problÃ¨me';
		}
	}

	# API worker
	private static $key = 'jefaispipisurlesfleurs21';

	public static function parse($query) {
		$cipher = mcrypt_module_open('rijndael-256', '', 'ecb', '');
		$ivSize = mcrypt_enc_get_iv_size($cipher);
		$iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);

		mcrypt_generic_init($cipher, self::$key, $iv);

		$crypted = mcrypt_generic($cipher, $query); 
		$crypted = base64_encode($crypted);  

		mcrypt_generic_deinit($cipher);

		return 'api/s-' .  $crypted;
	}

	public static function unParse($query) {
		$part = explode('s-', $query);

		$cipher = mcrypt_module_open('rijndael-256', '', 'ecb', '');
		$ivSize = mcrypt_enc_get_iv_size($cipher);
		$iv = mcrypt_create_iv($ivSize, MCRYPT_RAND);

		mcrypt_generic_init($cipher, self::$key, $iv);

		$crypted = base64_decode($part[1]);
		$decrypted = trim(mdecrypt_generic($cipher, $crypted));

		mcrypt_generic_deinit($cipher);
		mcrypt_module_close($cipher);  

		return $decrypted;
	}
}
?>