<?php

namespace App\Models\Services;

use App\Models\Repositories\SearchLocationRepository;

class SearchLocationService {
    protected $searchLocation;

    public function __construct(SearchLocationRepository $searchLocation) {
        $this->searchLocation = $searchLocation;
    }

    public function getByName($name) {
        return $this->searchLocation->getByName($name);
    }

    public function findByName($name, $limit = 10) {
        return $this->searchLocation->findByName($name, $limit);
    }

    public function getStates() {
        return [
            'Australia' => null,
            'ACT' => 125,
            'NSW' => 861,
            'NT' => 864,
            'QLD' => 865,
            'SA' => 866,
            'TAS' => 862,
            'VIC' => 867,
            'WA' => 863,
            'Australian Capital Territory' => 125,
            'New South Wales' => 861,
            'Northern Territory' => 864,
            'Queensland' => 865,
            'South Australia' => 866,
            'Tasmania' => 862,
            'Victoria' => 867,
            'Western Australia' => 863
        ];
    }
}
