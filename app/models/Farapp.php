<?php

class Farapp
{
	private $curl;
	private $params = array();
	private $method = null;

	public function __construct($method)
	{
		$this->curl = New Curl;
		$this->method = $method;
	}

    public function setParam($key, $value)
    {
    	$this->params[$key] = $value;
    	return $this;
    }

    public function setParams($parr)
    {
    	$this->params = array_merge($this->params, $parr);
    	return $this;
    }

    public function getPars()
    {
    	if (is_null($this->method))
    	{
    		Log::error('Something is really going wrong.');
    	}
    	return $this->curl->simple_get('http://vladivostok.farpost.ru' . $this->method, $this->params);
    }
}