<?php

class Farapp
{
    private static $instance;
	private static $curl;
	private static $params = array();
	private static $method = null;

	public static function getInstance($method = null)
	{
		self::$curl = New Curl;
		if ( ! is_null($method))
		{
			self::$method = $method;
		}
        if ( empty(self::$instance) ) {
            self::$instance = new self();
        }
        return self::$instance;
	}

	public function setMethod($method)
	{
		self::$method = $method;
		return $this;
	}

    public function setParam($key, $value)
    {
    	self::$params[$key] = $value;
    	return $this;
    }

    public function setParams($parr)
    {
    	self::$params = array_merge(self::$params, $parr);
    	return $this;
    }

    public function getPars()
    {
    	if (is_null(self::$method))
    	{
    		Log::error('Something is really going wrong.');
    	}
    	return self::$curl->simple_get('http://vladivostok.farpost.ru' . self::$method, self::$params);
    }
}