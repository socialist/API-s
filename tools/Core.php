<?php

namespace tools;

abstract class Core
{
	const ARRAY_RESULT = 0;
	const JSON_RESULT  = 1;
	const XML_RESULT   = 2;

	private $urlAPI;

	private $methods = ['POST', 'GET', 'PUT', 'DELETE', 'HEAD', 'PATCH', 'OPTIONS'];

	private $postData = [];

	private $responseHeaders = [];

	public function __construct($url)
	{
		$this->setUrlAPI($url);
	}

	public function getUrlAPI()
	{
		return $this->urlAPI;
	}

	public function setUrlAPI($url)
	{
		$this->urlAPI = $url;
	}

	abstract function buildUrl($request);

	protected function request($url, $method = 'GET', $resultType = 0)
	{
		if(!in_array($method, $this->methods)) {
			return false;
		}
		$curl = curl_init();

		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $this->headerRequest($resultType));
		curl_setopt($curl, CURLOPT_HEADER, true);

		if($method === 'POST') {
			curl_setopt($curl, CURLOPT_POSTFIELDS, $this->postData);
		}

		$result = curl_exec($curl);

		if($result === false) {
			throw new \Exception(curl_error($curl), curl_errno($curl));
		}

		curl_close($curl);
		$clearData = $this->cleanHeaders($result);

		if($resultType === self::ARRAY_RESULT) {
			return $this->jsonToArray($clearData);
		}
		return $clearData;
	}

	private function headerRequest($resultType)
	{
		switch($resultType) {
			case self::ARRAY_RESULT:
			case self::JSON_RESULT:
				return ['ContentType: Application/json', 'Accept: application/json'];
			break;

			case self::XML_RESULT:
				return ['ContentType: Application/xml', 'Accept: application/xml'];
		}
	}

	private function cleanHeaders($result)
	{
		$resultArray = explode("\n", trim($result));

		$result = array_pop($resultArray);
		$this->responseHeaders = $resultArray;

		return $result;
	}

	private function jsonToArray($data)
	{
		return json_decode($data);
	}
}