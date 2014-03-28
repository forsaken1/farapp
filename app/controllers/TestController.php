<?php

class TestController extends Controller
{

	public function showIndex()
	{
		$fapfap = new Farapp('/realty/rent_flats/');
		var_dump($fapfap->setParams(array(
			'price_min' => 10000,
			'price_max' => 15000,
			'areaTotal_min' => 10,
			'areaTotal_max' => 20,
			'flatType' => array('gostinka', 1, 2, 3),
		))->setParam('city', 1)->getPars());
	}

}