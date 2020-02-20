@extends('site.layouts.master')

@section('content')

<section>
<div class="container-fluid second_section"  >
	<div class="container container-whitebg " >
    <div class="row">
        <div class="col-sm-6 col-md-6 col-md-offset-3">
            <div class="panel panel-default">
              <div class="panel-heading">
                <label class="control-label">Forgot Password</label>
              </div>
              <div class="panel-body" style="padding-bottom: 30px;">
                <!-- error -->
                  @if($message != "")
                  <div class="alert {{ $alertType }}" role="alert">
                     {{ $message }}
                  </div>
                  @endif

                  <!-- /error -->
                  <form name="frmforgot" method="post" action="{{ route('forgot-password') }}">
                    <div class="form-group">
                      <label class="control-label">Email</label>
                      <input type="text" value="" name='email' class="form-control" />
                    </div>
                    <input type="hidden" value='{{ csrf_token() }}' name='_token' />
                    <button type="submit" class="btn btn-md btn-primary" style="float: right;">Submit</button>
                  </form>
                  <a href="/" class="btn btn-default btn-md" style="float: right; margin-right: 3px;">
                    Back to login
                  </a>
                  <br>
              </div>
            </div>
        </div> 
    </div>
</div>
</div></section>
 
@endsection
