<?php

class TestController extends Controller
{

	public function showIndex()
	{
		/*echo Farapp::getInstance()->setMethod('realty/rent_flats/')->setParams(array(
			'price_min' => 10000,
			'price_max' => 15000,
			'areaTotal_min' => 10,
			'areaTotal_max' => 20,
			'flatType' => array('gostinka', 1, 2, 3),
		))->setParam('city', 1)->getPars();*/

		//var_dump(Parser::GetRazdely());

		//print_r(Parser::getFlatPost('prodam-gostinku-na-russkoj-25248591'));

		var_dump(Parser::getPosts('realty/sell_flats', 5));
	}

}