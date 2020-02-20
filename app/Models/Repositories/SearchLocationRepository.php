<?php

namespace App\Models\Repositories;

use App\Models\Entities\SearchLocation;

class SearchLocationRepository {
	protected $searchLocation;

	public function __construct(SearchLocation $searchLocation) {
		$this->searchLocation = $searchLocation;
	}

	public function getAll() {
		return $this->searchLocation->get();
	}

	public function getByName($name) {
		return $this->searchLocation
			->where('name', '=', $name)
			->where('name', '!=', 'All Suburbs')
			->where(function($query) {
				$query->where(function($query) {
					$query->where('id', '>=', 75)
						->where('id', '<=', 182);
				});
				$query->orWhere(function($query) {
					$query->where('id', '>=', 3556)
						->where('id', '<=', 3560);
				});
			})
			->where('id', '!=', 115)
			->first();
	}

	public function find($id) {
		return $this->searchLocation->where('id', '=', $id)->first();
	}

	public function findByName($name, $limit = 10) {
		return $this->searchLocation
			->where('name', 'LIKE', '%' . $name . '%')
			->where('name', '!=', 'All Suburbs')
			->where('id', '>=', 75)
			->where(function($query) {
				$query->where(function($query) {
					$query->where('id', '>=', 75)
						->where('id', '<=', 182);
				});
				$query->orWhere(function($query) {
					$query->where('id', '>=', 3556)
						->where('id', '<=', 3560);
				});
			})
			->limit($limit)
			->get();
	}
}
