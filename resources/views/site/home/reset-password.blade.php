@extends('site.layouts.master')

@section('content')

<section>
<div class="container-fluid second_section" >
	<div class="container container-whitebg">
    <div class="row">
        <div class="col-sm-6 col-md-6 col-md-offset-3">
            <div class="panel panel-default">
              <div class="panel-heading">
                <label class="control-label">Reset Password</label>
              </div>
              <div class="panel-body">
                <!-- error -->
                  @if($message != "")
                  <div class="alert {{ $alertType }}" role="alert">
                     {{ $message }}
                  </div>
                  @endif
                  <!-- /error -->
                  @if(!$status)
                  <form name="frmReset" method="post" action="{{ URL::to('site/reset-password/'.$token) }}">
                    <div class="form-group">
                      <label class="control-label">New Password</label>
                      <input type="password" value="" name='password' class="form-control" autocomplete="off" />
                    </div>
                    <div class="form-group">
                      <label class="control-label">Re-type Password</label>
                      <input type="password" value="" name='password_confirmation' class="form-control" autocomplete="off" />
                    </div>
                    <div class="form-group text-right">
                      <button type="submit" class="btn btn-md btn-primary">Submit</button>
                    </div>
                    <input type="hidden" value='{{ csrf_token() }}' name='_token' />
                  </form>
                  @else
                    @if($roleID==1 || $roleID==2)
                      <div class="form-group text-center">
                        <a href="{{ URL::to('admin/login') }}"><button type="button" class="btn btn-md btn-primary">Go back to login page</button></a>
                      </div>
                    @else
                      <div class="form-group text-center">
                        <a href="{{ URL::to('site/login') }}"><button type="button" class="btn btn-md btn-primary">Go back to login page</button></a>
                      </div>
                    @endif
                  @endif
              </div>
            </div>
        </div>


        
    </div>
</div>
</div></section>
 
@endsection
