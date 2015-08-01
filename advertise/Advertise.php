<?php
namespace advertise;

use tools\Core;

class Advertise extends Core
{
	const COOKIE_NAME = 'adv_uid';
	private $cookieExpires = 31536000; // One year
	private $uid;

	public function __construct($url, $cookieExpires = 30)
	{
		parent::__construct($url);

		$this->cookieExpires = 86400 * $cookieExpires;
		$this->init();
	}

	private function init()
	{
		if(
			isset($_GET['uid']) 
			&& isset($_GET['utm_source']) 
			&& $_GET['utm_source'] === 'advertise'
		) {
			$this->uid = $_GET['uid'];
			set_cookie(
					self::COOKIE_NAME,
					$this->uid,
					$this->cookieExpires,
					'/'
				);
		}
	}
}