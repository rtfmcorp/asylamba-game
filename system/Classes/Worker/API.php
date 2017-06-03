<?php

namespace Asylamba\Classes\Worker;

use Asylamba\Classes\Library\Security;

class API {
	# API user
	/** @var string **/
	private $path;
	/** @var string **/
	private $serverId;
	/** @var string **/
	private $key;

	public $query;
	public $data;

	const TEMPLATE_INACTIVE_PLAYER = 51;
	const TEMPLATE_SPONSORSHIP = 52;

	/**
	 * @param Security $security
	 * @param string $serverId
	 * @param string $apiKey
	 * @param string $getOutRoot
	 */
	public function __construct(Security $security, $serverId, $apiKey, $getOutRoot) {
		$this->path = $getOutRoot;
		$this->serverId = $serverId;
		$this->key  = $apiKey;
		$this->security = $security;
	}

	private function query($api, $args) {
		$targ = '';
		$ch  = curl_init();

		foreach ($args as $k => $v) {
			$targ .= $k . '-' . $v . '/';
		}

		$this->query = $this->path . 'api/s-' . $this->serverId . '/a-' . $this->security->crypt('a-' . $api . '/' . $targ, $this->key);
		
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

	public function confirmInscription($bindkey) {
		if ($this->query('confirminscription', array('bindkey' => $bindkey, 'serverid' => $this->serverId))) {
			if ($this->data['statement'] == 'success') {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	public function confirmConnection($bindkey) {
		if ($this->query('confirmconnection', array('bindkey' => $bindkey, 'serverid' => $this->serverId))) {
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

	public function sendMail($bindkey, $template) {
		if ($this->query('sendmail', array('bindkey' => $bindkey, 'serverid' => $this->serverId, 'template' => $template))) {
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

	public function abandonServer($bindkey) {
		if ($this->query('abandonserver', array('bindkey' => $bindkey, 'serverid' => $this->serverId))) {
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
