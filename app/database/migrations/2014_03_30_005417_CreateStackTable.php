<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStackTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('stack', function(Blueprint $table)
		{
			$table->increments('id');
			
			$table->string('key')->nullable();
			$table->string('link')->nullable();
			$table->string('subject')->nullable();
			$table->string('price')->nullable();
			$table->text('annotation')->nullable();
			$table->string('district')->nullable();
			$table->string('flatType')->nullable();
			$table->string('street')->nullable();
			$table->string('area')->nullable();
			$table->text('text')->nullable();
			#auto
			$table->string('model')->nullable();
			$table->string('year')->nullable();
			$table->string('displacement')->nullable();
			$table->string('transmission')->nullable();
			$table->string('drive')->nullable();
			$table->string('fuel')->nullable();
			$table->string('hasDocuments')->nullable();
			$table->string('hasRussianMileage')->nullable();
			$table->string('isAfterCrash')->nullable();
			$table->string('condition')->nullable();
			$table->string('guarantee')->nullable();
			$table->string('author')->nullable();
			#work
			$table->string('payment')->nullable();
			$table->string('paymentform')->nullable();
			$table->string('firm')->nullable();
			$table->string('branch')->nullable();
			$table->string('vacancy')->nullable();
			$table->string('employment')->nullable();
			$table->string('obligation')->nullable();
			$table->string('education')->nullable();
			$table->string('experience')->nullable();

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('stack');
	}

}
