<?php

class SearchController extends BaseController {

	public function search($query)
	{
		// $result = SearchHelper::search($query);
		$result = ['id' => 1, 'title' => 'item #1', 'desc' => 'desc #1']; // example

		return Response::json($result);
	}
}