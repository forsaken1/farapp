<?php

class CategoryTableSeeder extends Seeder {

	public function run()
	{
		DB::table('categories')->truncate();

		$categories[] = array(
			'title' => 'Автомобили',
		);

		$categories[] = array(
			'title' => 'Недвижимость',
		);

		$categories[] = array(
			'title' => 'Работа',
		);

		$categories[] = array(
			'title' => 'Бесплатное',
		);

		DB::table('categories')->insert($categories);
	}

}