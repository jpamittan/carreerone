@extends('site.layouts.master')

@section('content')

<section>
<div class="container-fluid second_section" >
	<div class="container container-whitebg"  >
    <div class="row" >
        <div class="col-sm-6 col-md-4 col-md-offset-4">
            <div  >
              <!-- <h1 class="text-center login-title">Sign in to continue to Bootsnipp</h1> -->
              <div class="panel panel-default">
                  <div class="panel-heading">
                      <label class="control-label">Login</label>
                  </div>
                  <div class="panel-body">
                    <!-- error -->
                      @if(!empty(\Session::get('message')))
                      <div class="alert alert-danger">
                         {{\Session::get('message')}}
                      </div>
                      @endif
                      
                      @if(!empty(\Session::get('activated')))
                      <div class="alert alert-success">
                         Your account has been activated successfully.
                      </div>
                      @endif
                      
                     <!-- /error -->
                    <form id="login-form" action="{{URL::route('site-signin')}}" method="post" class=" form-signin">
                      <div class="form-group">
                        <label class="control-label">Email</label>
                        <input type="text" class="form-control" id="email" name="email" placeholder="Email" required autofocus>
                      </div>
                      <div class="form-group clearfix">
                        <label class="control-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <div class="checkbox pull-right">
                          <label style="margin-right: 0 !important;">
                            <input type='checkbox' value='remember-me' style="margin-top: 4px !important;" />
                            <span>Remember me</span>
                          </label>
                        </div>
                      </div>
                      <div class="form-group clearfix">
                        <button type="submit" class="btn btn-md btn-primary col-md-12"><span class='glyphicon glyphicon-lock'></span>&nbsp;Sign in</button>
                      </div>
                      <div class="form-group">
                        <a href='{{ URL::to("site/forgot-password") }}'>Forgot password?</a>
                      </div>
                      <input type="hidden" value='{{ csrf_token() }}' name='_token' />
                    </form>
                  </div>
              </div>
            </div>
        </div>


        
    </div>
</div>
</div></section>
 
@endsection
