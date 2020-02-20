<?php

namespace App\Models\Services;

use App\Models\Repositories\LocationRepository;

class LocationService {
    protected $location;

    public function __construct(LocationRepository $location) {
        $this->location = $location;
    }

    public function findBySuburbCombination($suburb, $state, $postcode = null) {
        return $this->location->findBySuburbCombination($suburb, $state, $postcode);
    }

    public function findByPostcode($postcode) {
    	return $this->location->findByPostcode($postcode);
    }

    public function findBySuburb($suburb) {
    	return $this->location->findBySuburb($suburb);
    }

    public function getAutocompleteLocations($input, $format, $limit=5) {
        $locations = $this->location->getAutocompleteByPostcode($input, $limit);
        $ret = [];
        if ($format != 'jqui') {
            $ret['suggestions'] = [];
        }
        if ($locations) {
            foreach ($locations as $location) {
                if ($format != 'jqui') {
                    $ret['suggestions'][] = [
                        'value' => $location['combi'],
                        'data' => [
                            'postcode' => $location['post_code'],
                            'state' => $location['state'],
                            'country' => $location['country_code'],
                            'city' => $location['city']
                        ]
                    ];
                } else {
                    $ret[] = [
                        'value' => $location['combi'],
                        'label' => $location['combi'],
                        'data' => [
                            'postcode' => $location['post_code'],
                            'state' => $location['state'],
                            'country' => $location['country_code'],
                            'city' => $location['city']
                        ]
                    ];
                }
            }
        }
        return $ret;
    }
}
