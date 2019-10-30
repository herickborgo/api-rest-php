<?php

namespace Config\Controller;

class Controller
{
	public $service = null;

    public function __construct()
    {
    	$controller = get_class($this);
    	$serviceClass = str_replace('Controller', 'Service', $controller);
    	if (class_exists($serviceClass)) {
    		$this->service = new $serviceClass();
    	}
    }
}
