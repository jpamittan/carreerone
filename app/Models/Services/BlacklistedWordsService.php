<?php
namespace App\Models\Services;
use App\Models\Repositories\BlacklistedWordsRepository;

class BlacklistedWordsService {
	private $blacklistedWordsRepository;
	
	function __construct(){
		$blacklistedWordsRepository = new BlacklistedWordsRepository();
		$this->blacklistedWordsRepository = $blacklistedWordsRepository;
	}
	
	function getAllWords(){
		return $this->blacklistedWordsRepository->getAll();
	}
}