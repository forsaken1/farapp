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

		//var_dump(Parser::getCarPost('prodam-mikroavtobus-26483298'));

		//var_dump(Parser::getPosts('realty/sell_flats', 5));

		//var_dump(Parser::getPosts('auto/sale', 5, 5));

		var_dump(Parser::getFreePost('zamechatelnye-shenki-v-dobrye-ruki-26606856'));
	}

}