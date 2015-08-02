<?php
namespace advertise;

use tools\Core;

class Advertise extends Core
{
	const COOKIE_NAME = 'adv_uid';
	private $cookieExpires = 31536000; // One year
	private $uid;
	private $keyOffer;
	private $requestParams = [];

	/**
	 * Statuses
	 */
	const STATUS_TAKEN      = 1;
	const STATUS_PROCESSING = 2;
	const STATUS_CANCELED   = 3;
	const STATUS_NOT_EXISTS = 4;

	public function __construct($keyOffer, $cookieExpires = 30)
	{
		parent::__construct('//yii.local/');

		$this->cookieExpires = time() + (86400 * $cookieExpires);
		$this->keyOffer = $keyOffer;

		$this->init();
	}

	public function noticeImage($params = [])
	{
		$params['uid'] = $this->uid;

		$this->setParams($params);
		
		return '<img src="' . $this->buildUrl('/img') . '" width="1" height="1" alt="" />';
	}

	public function noticeScript($params = [])
	{
		$params['tracking'] = $this->keyOffer;
		$params['uid'] = $this->uid;
		$this->setParams($params);
		$script = '<script type="text/javascript">';

		foreach($this->requestParams as $key => $param) {
			$script .= 'var cpa_' . $key . ' = \'' . $param . "';\n";
		}

		$script .= "</script>\n";
		$script .= '<script type="text/javascript" src="' . $this->getUrlAPI() . 'js/tracking.js"></script>';

		return $script;
	}

	public function noticeCurl($params = [])
	{
		$params['uid'] = $this->uid;
		$this->setParams($params);

		$url = $this->buildUrl('/server');

		return $this->request('http:'.$url);
	}

	public function setStatusRequest($status, $token)
	{
		$this->setParams(['token' => $token]);

		echo $this->buildStatusUrl();
		return $this->request($this->buildStatusUrl());
	}

	public function setParams($params)
	{
		if(!is_array($params)) {
			throw new Exception('$params must be array');
		}

		$this->requestParams = array_merge($this->requestParams, $params);
	}

	public function deleteParams($params)
	{
		if(is_string($params)) {
			$params = str_replace(' ', '', $params);
			$params = explode(',', $params);
		}

		if(is_array($params)) {
			foreach($params as $key) {
				if(array_key_exists($key, $this->requestParams)) {
					unset($this->requestParams[$key]);
				}
			}
		}
	}

	public function clearParams()
	{
		$this->requestParams = [];
	}

	public function buildUrl($request)
	{
		$url = $this->getUrlAPI() . 'tracking/' . $this->keyOffer . $request;
		if(count($this->requestParams) > 0) {
			$url .= '?' . $this->buildParams($this->requestParams);
		}
		return $url;
	}

	public function buildStatusUrl()
	{
		$url = 'http:' . $this->getUrlAPI() . 'action_status/' . $this->keyOffer . '/';
		if(count($this->requestParams) > 0) {
			$url .= '?' . $this->buildParams($this->requestParams);
		}
		return $url;
	}

	private function init()
	{
		if(
			isset($_GET['uid']) 
			&& isset($_GET['utm_source']) 
			&& $_GET['utm_source'] === 'advertise'
		) {
			$this->uid = $_GET['uid'];
			\setcookie(
					self::COOKIE_NAME,
					$this->uid,
					$this->cookieExpires,
					'/'
				);
		} else {
			$this->uid = $_COOKIE[self::COOKIE_NAME];
		}
	}

	private function buildParams($params)
	{
		$strParams = '';
		foreach($params as $key => $param) {
			$strParams[] = $key . '=' . $param;
		}

		return implode('&', $strParams);
	}
}