<?php

namespace App\Http\Controllers\site;

use App\Http\Requests;
use App\Http\Controllers\site\AdminController;
use App\Models\Services\ExpiredJobService;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use View, Redirect, Response;

class ExpiredJobController extends AdminController {
    public function expiredJob() {
      $service = new ExpiredJobService();
      $service->expiredJob();
    }
}
