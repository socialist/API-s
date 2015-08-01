<?php

namespace tools;

class Autoloader
{
	public function __construct(){}

	public static function autoload($class)
	{
		$file = str_replace('\\', '/', $class);
		$filePath = dirname(__DIR__) . '/' . $file . '.php';
		if(file_exists($filePath)) {
			require_once($filePath);
		}
	}
}

spl_autoload_register('tools\Autoloader::autoload');