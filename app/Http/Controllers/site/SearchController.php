<?php

namespace App\Http\Controllers\site;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use App\Http\Controllers\site\AdminController;
use App\Models\Services\JobAssetService;
use App\Models\Services\Purifier;
use App\Models\Services\JsxService;
use App\Models\Services\SearchLocationService;
use App\Models\Services\LocationService;
use Illuminate\Pagination\Paginator;
use View, Redirect, Response, Session;

class SearchController extends AdminController {
    protected $featuredJob = null;
    protected $jsx = null;
    protected $category = null;
    protected $searchLocation = null;
    protected $indeed = null;
    protected $geoLocation = null;
    protected $jobAlert = null;
    protected $ghosting = null;
    protected $source = null;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct(
        JsxService $jsx,
        SearchLocationService $searchLocation,
        LocationService $location
    ) {
        $this->jsx = $jsx;
        $this->searchLocation = $searchLocation;
        $this->location = $location;
    }
     
    public function careeronejobs() {
        $params = Input::all();
        $page = (int) Input::get('page', 1);
        $cpage = (int) Input::get('page', 1);
        $page = ($page > 50) ? 50 : $page;
        $search_location_get =  Input::get('search_location');
        $serviceJobAssetService = new JobAssetService();
        $slocation = $serviceJobAssetService->getLocation();
        $user_location = $serviceJobAssetService->getUserLocation();
        if (empty($search_location_get))
            if ($search_location_get != '' && !isset($params['search_location']))
                if (!empty($user_location)) {
                    $params['search_location'] = $user_location[0]->location;
                }
            }
        }
        $jobs = $this->jsx->applyParams($params)->setPage($page)->search();
        $query = $this->jsx->getQuery();
        $locationId = $this->jsx->getLocationId();
        $locationName = $this->jsx->getLocationName();
        $params['location_id'] = $locationId;
        $jobCount = $jobs['Jobs']['@attributes']['Found'];
        if (empty($search_location_get)) {
            if (($search_location_get != '' && !isset($params['search_location']))) {
                if (!empty($user_location)) {
                    $params['search_location'] = $user_location[0]->location;
                }
            } 
        }
        if ($jobCount != 1000) {
            $page = ceil($jobs['Jobs']['@attributes']['Found'] / 20);
            $page = ($page < 0) ? 1 : $page;
            $jobCount = ($jobCount > 1000) ? 1000 : $jobCount;
            $jobCount = ($jobCount < 0) ? 0 : $jobCount;
        }
        $paginator = null;
        $alljobs = array();
        if (!empty($jobs) && !empty($jobs['Jobs']) && !empty($jobs['Jobs']['Job'])) {
            $alljobs =$jobs['Jobs']['Job'];
        }
        if (isset($jobs['Jobs']['Job'])) {
            $paginator = new Paginator([], 20, $page ,['total' => $jobCount, 'currentpage' => $cpage , 'perpage' => 20] );
        }
        $fullparams='';
        foreach($params as $k=>$v) {
            if ($k == 'page' || $k == 'location_id') {}else{ 
                $fullparams .= $k.'='.$v."&";
            }
        }
        $view = View::make(
            'site.home.careeronejobs', 
            [
                'jobCount' => $jobCount,
                'jobs' => isset($alljobs) ? $alljobs : [],
                'params' => $params,
                'fullparams' => $fullparams,
                'paginator' => $paginator,
                'query' => $query
            ]
        );
        return Response::make($view);
    }

    public function getAreas() {
        $keyword = Input::get('keyword');
        $limit = (int) Input::get('limit', 10);
        if (!$keyword) {
            return Response::json([]);
        }
        $results = $this->searchLocation->findByName($keyword, $limit);
        $states = $this->searchLocation->getStates();
        $json = [];
        foreach ($results as $key => $result) {
            $json[] = $result->name;
        }
        foreach ($states as $name => $id) {
            if (stripos($name, $keyword) !== false) {
                $json[] = $name;
            }
        }
        return Response::json($json);
    }

    public function autocomplete() {
        $query = Input::get('query');
        $format = Input::get('format');
        $results = $this->location->getAutocompleteLocations($query, $format);
        return Response::json($results);
    }
}
