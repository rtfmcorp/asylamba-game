<?php

namespace App\Classes\Worker;

use App\Classes\Library\Security;

class API
{
	public string $query;
	public array $data;

	const TEMPLATE_INACTIVE_PLAYER = 51;
	const TEMPLATE_SPONSORSHIP = 52;

	public function __construct(
		protected Security $security,
		protected string $serverId,
		protected string $apiKey,
		protected string $getOutRoot,
	) {
	}

	private function query(string $api, array $args): bool
	{
		$targ = '';
		$ch  = \curl_init();

		foreach ($args as $k => $v) {
			$targ .= $k . '-' . $v . '/';
		}

		$this->query = $this->getOutRoot . 'api/s-' . $this->serverId . '/a-' . $this->security->crypt('a-' . $api . '/' . $targ, $this->apiKey);
		
		\curl_setopt($ch, CURLOPT_URL, $this->query);
		\curl_setopt($ch, CURLOPT_RETURNTRANSFER , TRUE);
		$answer = \curl_exec($ch);
		\curl_close($ch);
		
		if ($answer !== FALSE) {
			$this->data = \unserialize($answer);
			return true;
		}
		return false;
	}

	public function userExist($bindkey) {
		if ($this->query('userexist', array('bindkey' => $bindkey))) {
			return ($this->data['statement'] == 'success');
		}
		return false;
	}

	public function confirmInscription($bindkey) {
		if ($this->query('confirminscription', array('bindkey' => $bindkey, 'serverid' => $this->serverId))) {
			return ($this->data['statement'] == 'success');
		}
		return false;
	}

	public function confirmConnection($bindkey) {
		if ($this->query('confirmconnection', array('bindkey' => $bindkey, 'serverid' => $this->serverId))) {
			return ($this->data['statement'] == 'success');
		}
		return false;
	}

	public function playerIsDead($bindkey, $serverId) {
		if ($this->query('playerisdead', array('bindkey' => $bindkey, 'serverid' => $serverId))) {
			return ($this->data['statement'] == 'success');
		}
		return false;
	}

	public function sendMail($bindkey, $template) {
		if ($this->query('sendmail', array('bindkey' => $bindkey, 'serverid' => $this->serverId, 'template' => $template))) {
			return ($this->data['statement'] == 'success');
		}
		return false;
	}

	public function sendMail2($email, $serverId, $template, $playerId) {
		if ($this->query('sendmail2', array('email' => $email, 'serverid' => $serverId, 'template' => $template, 'playerid' => $playerId))) {
			return ($this->data['statement'] == 'success');
		}
		return false;
	}

	public function abandonServer($bindkey) {
		if ($this->query('abandonserver', array('bindkey' => $bindkey, 'serverid' => $this->serverId))) {
			return ($this->data['statement'] == 'success');
		}
		return false;
	}
}
