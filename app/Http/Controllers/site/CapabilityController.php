<?php

namespace App\Http\Controllers\site;

use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\site\AdminController;
use App\Models\Services\CapabilityAssetService;
use View, Redirect, Response;

class CapabilityController extends AdminController {
    public function matchCapability() {
      $service = new CapabilityAssetService();
      $service->matchCapability();
    }
}
