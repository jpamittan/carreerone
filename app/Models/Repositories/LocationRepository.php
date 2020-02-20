<?php

namespace App\Models\Repositories;

use App\Models\Entities\Location;

class LocationRepository {
	protected $location;

	public function __construct(Location $location) {
		$this->location = $location;
	}

	public function getAll() {
		return $this->location->get();
	}

	public function find($id) {
		return $this->location->where('id', '=', $id)->first();
	}

	public function findBySuburbCombination($suburb, $state, $postcode = null) {
		if ($postcode) {
			return $this->location->where('Combi', '=', $suburb . ' ' . $state . ' ' . $postcode)->first();
		}
		return $this->location->where('City', '=', $suburb)->where('State', '=', $state)->first();
	}

	public function findByPostcode($postcode) {
		return $this->location->where('PostalCode', '=', $postcode)->first();
	}

	public function findBySuburb($suburb) {
		return $this->location->where('City', 'LIKE', $suburb . '%')->first();
	}

    public function getAutocompleteByPostcode($postcode, $limit=5) {
        if (strlen($postcode) > 2) {
            return $this->location
                ->where('post_code', 'LIKE', $postcode . '%')
                ->orWhere('city', 'LIKE', $postcode . '%')
                ->where('id', '!=' , 3745) //CR45
                ->take($limit)
                ->orderBy('post_code', 'ASC')
                ->orderBy('city', 'ASC')
                ->get();
        } else {
            return array();
        }
    }

    public function getByPostcode($postcode) {
        return $this->location->where('post_code', '=', $postcode)->first();
    }
}
