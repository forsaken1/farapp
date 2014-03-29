<?php
/**
 * Класс получения данных для парсинга
 */

class Farapp
{
    private static $instance;
	private static $curl = null;
	private static $params = array();
	private static $method = null;
	private static $url = 'http://vladivostok.farpost.ru';

    private function __construct() {}
    private function __clone() {}
    private function __wakeup() {}

	public static function getInstance($method = null)
	{
		self::$curl = (is_null(self::$curl)) ? New Curl : self::$curl;
		self::$method = $method;

        return (empty(self::$instance)) ? new self() : self::$instance;
	}

    /**
     * Установка адреса запроса
     * @param $method Строка запроса
     * @return self
     */
	public function setMethod($method)
	{
		self::$method = $method;
		return $this;
	}

    /**
     * Установка одного параметра
     * @param $key Ключ параметра
     * @param $value Значение параметра
     * @return self
     */
    public function setParam($key, $value)
    {
    	self::$params[$key] = $value;
    	return $this;
    }

    /**
     * Установка группы параметров
     * @param $parr Массив параметров
     * @return self
     */
    public function setParams($parr)
    {
    	self::$params = array_merge(self::$params, $parr);
    	return $this;
    }

    /**
     * Получение установленного юрла
     * @return string
     */
    public function getURL()
    {
    	return self::$url  . '/' . self::$method;
    }

    /**
     * Получение данных курла
     * @return string
     */
    public function getPars()
    {
    	if (is_null(self::$method))
    	{
    		Log::error('Something is really going wrong.');
    	}
    	// iconv('cp1251', 'UTF8', ...);
    	$response = self::$curl->simple_get(self::$url . '/' . self::$method, self::$params);
    	return str_get_html(iconv('cp1251', 'UTF8', $response));
    }
}