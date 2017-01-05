<?php

namespace Asylamba\Classes\Worker;

use Asylamba\Classes\Library\Security;

class API {
	# API user
	/** @var string **/
	private $path;
	/** @var string **/
	private $server;
	/** @var string **/
	private $key;

	public $query;
	public $data;

	const TEMPLATE_INACTIVE_PLAYER = 51;
	const TEMPLATE_SPONSORSHIP = 52;

	public function __construct($path, $server, $key) {
		$this->path = $path;
		$this->server = $server;
		$this->key  = $key;
		$this->security = new Security();
	}

	private function query($api, $args) {
		$targ = '';
		$ch  = curl_init();

		foreach ($args as $k => $v) {
			$targ .= $k . '-' . $v . '/';
		}

		$this->query = $this->path . 'api/s-' . $this->server . '/a-' . $this->security->crypt('a-' . $api . '/' . $targ, $this->key);

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

	public function sendMail2($email, $serverId, $template, $playerId) {
		if ($this->query('sendmail2', array('email' => $email, 'serverid' => $serverId, 'template' => $template, 'playerid' => $playerId))) {
			if ($this->data['statement'] == 'success') {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	public function abandonServer($bindkey, $serverId) {
		if ($this->query('abandonserver', array('bindkey' => $bindkey, 'serverid' => $serverId))) {
			if ($this->data['statement'] == 'success') {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	# TODO
	public function getPlayerStatement($bindkey) {
		# a faire
		return FALSE;
		return $this->query('getplayerstatement', array('bindkey' => $bindkey));
	}
}
