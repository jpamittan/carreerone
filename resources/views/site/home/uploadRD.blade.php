@extends('site.layouts.master')
@section('content')
<?php $t =\Session::all(); ?>
<section>
	<div class="container apply-paddingnone" style="background-color: white;">
		<div class = "col-md-12  bck-color">
			<div class= "col-xs-12 col-sm-12 col-md-12 jobview-header">
				@include('site.partials.jobtitle-sch',array('job' => $job))
				<div class ="col-xs-12 col-sm-12 col-md-12">
					<strong><h5 class="form-color">Please upload the Role Description</h5></strong>
				</div>
			   	<div class ="col-xs-12 col-sm-12 col-md-12">
					<div id="collapseOne" class="pull-left accordion-inner accordion-body accord-apply collapse in">
						<!-- error -->
						@if(!empty(\Session::get('error')))
							<div class="alert alert-danger">{{\Session::get('error')}}</div>
						@endif
	           			<!-- /error -->
						<!-- <form role="form"> -->
						<form id="ins-frm" name="ins-frm" action="{{URL::route('recruiter-upload-roledesc',['job_id'=>$job->id])}}" method="post" enctype='multipart/form-data'> 
		 					<input type="hidden" name="_token" id="token" value="{{{csrf_token()}}}"> 
							<div class="col-sm-12 col-md-12 " >
								<h5><span class="uploadResume" style="font-weight: bold;">Upload Role Description</span></h5>
								<div class="col-xs-12 col-sm-12 col-md-12 browse-width">
									<input type="file" class="form-control form-ctrl1" id="inputfile" name="inputfile" value = "No File Choosen">
							    </div>
							</div>
							<div class="col-sm-12 col-md-12" style="padding-top:30px;">
								<h5><span class="uploadResume" style="font-weight: bold;">Please upload or enter the advertisement below</span></h5>
								<div class="col-xs-12 col-sm-12 col-md-12 browse-width">
									<input type="file" class="form-control form-ctrl1" id="inputfileadvert" name="inputfileadvert" value = "No File Choosen">
							    </div>
							</div>
							<div class="col-sm-12 col-md-12" style="padding-top:10px;">  
								<textarea class="col-md-11" rows="8" id="advert" name="advert">
									@if(!empty(\Session::get('_old_input.advert'))){{\Session::get('_old_input.advert')}}@endif
								</textarea>
							</div>
							<div class="col-sm-12 col-md-12" style="padding-top:30px;">
								<h5><span class="uploadResume" style="font-weight: bold;">Hiring Manager</span></h5>
							</div>
							<div class="col-sm-12 col-md-12" style="padding-top:10px;">  
								<b>Name :</b> <input type="text" id='hiring_name' name='hiring_name'>
								<b>Phone :</b> <input type="text" id='hiring_phone' name='hiring_phone'>
								<b>Email :</b> <input type="text" id='hiring_email' name='hiring_email'>
							</div>
							<div class="col-sm-12 col-md-12" style="padding-top:30px;" >
								<h5><span class="uploadResume" style="font-weight: bold;">Workplace location</span></h5>
							</div>
							<div class="col-sm-12 col-md-12" style="padding-top:10px;" >  
								<Select id='workplace' name='workplace'> 
									<option value=""> Select suburb</option>
									@foreach($suburbs as $suburb)
										<option value="{{$suburb->ins_suburb_id}}"> {{$suburb->suburb}}</option>
									@endforeach
								</Select>
							</div>
							<div class="col-sm-12 col-md-12 " style="padding-top:30px;" >
								<h5><span class="uploadResume" style="font-weight: bold;">Length of fixed term</span></h5>
							</div>
							<div class="col-sm-12 col-md-12 " style="padding-top:10px;" >  
								<Select id='length_term' name='length_term'>
									<option value=""> Select term length</option>
									<option value="121660000"> 6 months</option>
									<option value="121660001"> 12 months</option>
									<option value="121660002"> 2 years</option>
									<option value="121660003"> 3 years</option>
									<option value="121660004"> 4 years</option>
									<option value="121660005"> 5 years</option>
									<option value="121660006"> Other</option>
								</Select>
								<input type="text" id='length_term_other' name='length_term_other' style="display :none;">
							</div>
							<div class="buttonjobview">
								<button type="submit" id="applybutton5" name="status" class="btn btn-primary rd-sub">Submit</button>
							</div>
						</form>
				    </div>
				</div>
				@include('site.partials.job-description-sch' ,array('job' => $job))
			</div>
		</div>
	</div>
</section>
<script>
	$("#length_term").change(function() {
		if($(this).val() == 121660006) {
			$( "#length_term_other" ).show();
		} else {
			$( "#length_term_other" ).hide();
			$( "#length_term_other" ).val('');
		}
	});
</script>						
@endsection
