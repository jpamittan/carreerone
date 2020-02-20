<?php

namespace App\Models\Repositories;
use App\Models\Entities\BlacklistedWords;

class BlacklistedWordsRepository {
	function getAll(){
		$BlacklistedWords = BlacklistedWords::orderBy('id','asc')->get();
		$arrays = array();
		foreach($BlacklistedWords as $object){
		    $arrays[] = $object->word;
		}
		return $arrays;
	}
}
