@extends('site.layouts.master')

@section('content')

<div class="container container-whitebg">

@if($user)
<div class="row">
        
	<div class="row ">
	<div class="col-sm-4"></div>
	<div class="col-sm-4">
	<h4 style="text-align: center;">Welcome {{$user->first_name}} {{$user->last_name}}</h4>
	<h5 style="text-align: center;">Please enter a new password and activate your account.</h5>
	<div class="alert alert-danger" id="frmActivate_alert" style="display: none;">
  	
	</div>
	<form class="form-horizontal" method="post" id="frmActivate" action="{{ URL::route('user-activate-post' , $user->password) }}" >
	 {{ csrf_field() }}
  	<div class="form-group">
    <label for="exampleInputPassword1">Password</label>
      <input type="password" class="form-control" id="password" name="password" placeholder="Enter Password" required="required">
  	</div>
  	<div class="form-group">
    <label for="exampleInputConfirmPassword1">Confirm Password</label>
      <input type="password" class="form-control" name="confirm_password" id="confirm_password" placeholder="Confirm password" required="required">
  	</div>
  	<div class="form-group"> 
      <button type="button" id="btn_activate" class="btn btn-lg btn-primary btn-block">Activate</button>
  	</div>
	</form>
	<div class="col-sm-4"></div>
	</div>
</div>
@else
<div class="row">
<div class="col-sm-4"></div>
<div class="col-sm-4">
<div class="alert alert-danger" id="frmActivate_alert">
  	Invalid activation link.
	</div>
</div>	
<div class="col-sm-4"></div>	
</div>
@endif

<script type="text/javascript">

$(document).ready(function(){

$('#btn_activate').click(function(e){

	var regularExpression = /^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{6,16}$/;
	
	if(!regularExpression.test($('#password').val())){
		$('#frmActivate_alert').text('Password should contain minimum 6 characters including at least one number and one special character.');
		$('#frmActivate_alert').show();
		return false;
	}
	
	if($('#password').val()!=$('#confirm_password').val()){

		$('#frmActivate_alert').text('Password doesn\'t match !');
		
		$('#frmActivate_alert').show();

		return false;
		
	}else{

		$('#frmActivate_alert').hide();
	}

	
$('#frmActivate').submit();

	
});
	
});

</script>
@endsection