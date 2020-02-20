@extends('site.layouts.master-dashboard')
@section('content')
<section>
	<div class="container apply-paddingnone" style="background-color: white;">
		<div class = "col-md-12  bck-color">
			<div class= "col-xs-12 col-sm-12 col-md-8 jobview-header">
				@include('site.partials.jobtitle-sch',array('job' => $job))
				@if(empty($job->job_apply))
					<div class ="col-xs-12 col-sm-12 col-md-12 form-margin">
						<div class= "col-xs-10 col-sm-10 col-md-8">
							<strong> <h5 class = "form-color" >APPLICATION FORM</h5></strong>
						</div>
						<div class= "col-xs-2 col-sm-12 col-md-4">
							<div class = "accordion-group accordion-caret">
								<span class = "pull-right form-border1"><a class="accordion-toggle " data-toggle="collapse" href="#collapseOne"></a></span>
							</div>
						</div>
					</div>
					<div class ="col-xs-12 col-sm-12 col-md-12">
						<div id="collapseOne" class="pull-left accordion-inner accordion-body accord-apply collapse in">
							<!-- error -->
							@if(!empty(\Session::get('message')))
								<div class="alert alert-danger">
									{{\Session::get('message')}}
								</div>
							@endif
							<!-- /error -->
							<!-- <form role="form"> -->
							<form id="ins-frm" name="ins-frm" action="{{URL::route('site-post-file',['job_id' => $job->id]) }}" method="post" enctype='multipart/form-data'> 
								<input type="hidden" name="_token" id="token" value="{{{csrf_token() }}}"> 
								<div class="col-sm-12 col-md-12 " >
									<h5><span class="uploadResume">Select Resume to apply</h5>
									@if(isset($resume))

									 @foreach($resume as $userresume)
										<div class="checkbox">
											<label>
												<input style='margin-right: 10px;' type="radio" id="resume" name="resume" value="{{$userresume->id}}">{{$userresume->resume_name}} 

												(
													@if($userresume->category_id == 500) 
		                                               Master
			                                        @elseif($userresume->category_id == 501) 
			                                            Draft
			                                        @else
			                                          {{$userresume->category_name}}
			                                         
			                                        @endif
		                                        )
												<?php /* <input type="hidden" id="resume_id" name="resume_id" value="{{$resume->id}}"> */ ?>
											</label>
										</div>
 									@endforeach
 									<h5><span class="uploadResume">Or select file to upload</h5>
									@endif

								 	
									<div class="col-xs-12 col-sm-12 col-md-12 browse-width">
										<input type="file" class="form-control form-ctrl1" id="inputfile" name="inputfile">
									</div>

								 
									<!-- <div class= "col-xs-4 col-sm-4 col-md-6">
									<label class="control-label browse-size browseStyle" for="input-2">Browse</label>
									</div> -->
								</div>

<?php /* 
								<div class="col-xs-12 col-sm-12 col-md-12 coverLetter" >
									<h5 class="uploadLetter">Upload advert</h5>
									<div class="col-xs-8 col-sm-8 col-md-6 browse-width">
										<input type="file" class="form-control form-ctrl1" id="advert" name="advert" value = "Please select file">
									</div>
								</div>
	*/ ?>

								<?php /* 
								<div class="col-xs-12 col-sm-12 col-md-12 coverLetter" >
									<h5 class="uploadLetter">Upload Cover Letter</h5>
									<div class="col-xs-8 col-sm-8 col-md-6 browse-width">
										<input type="file" class="form-control form-ctrl1" id="coveringletter" name="coveringletter" value = "No File Choosen">
									</div>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-12 " >
									<p>&nbsp;<p>
									<p>Please Upload Any other files to support your  Application</p>
									<div class="col-xs-8 col-sm-8 col-md-6 browse-width ">
										<input type="file" class="form-control form-ctrl1" id="supporting_file" name="supporting_file" value = "optional">
									</div>
								</div>
								*/
								?>

								<div class="col-xs-12 col-sm-12 col-md-12 "  style="margin-top:15px; margin-bottom: 15px;">
								@if(!empty($job->job_draft)) 
                                     <button   class="btn btn-primary" disabled>Application Draft</button>
                             	@else
															 
										<div class="buttonjobview">
											<button type="reset" id="resetbutton"  name="status" value="1" class="btn btn-primary">Clear</button>
											<button type="submit" id="applybutton5"  name="status" value="1" class="btn btn-primary">Submit</button>
 	
 										<?php /* 
 										@if(empty($job->isjobfromdraft)) 
											<button type="submit" id="saveasdraft" name="status" value="0" class="btn btn-primary" style="background-color: #600f4d;">Save As Draft</button>

										@endif
										*/
										?>
										</div>

								@endif
								</div>
							</form>
							<!-- </form> -->
						</div>
					</div>
				@endif
				@include('site.partials.job-description-sch' ,array('job' => $job))
			</div>
			<div class="col-xs-12 col-sm-12 col-md-4">
				@include('site.partials.capabilitymatch')
			</div>
		</div>
	</div>
</section>
@endsection
<script src="https://code.jquery.com/jquery-2.1.4.js"></script>
<!-- <script>
	$(function() {
		$("#applybutton5").click(function(e){
			e.preventDefault();
				var data = new FormData(document.forms.namedItem('ins-frm'));
			$.ajax({
			  type: "POST",
			  url: "{{URL::route('site-post-file',['job_id' => $job->id]) }}",
			  data:data,
			  success: function(msg){
			  	alert(msg);

			 // window.location.href = "{{URL::route('site.home.dashboard')}}";
			  },
			    error: function(jqXHR, textStatus, errorThrown) {
			     $errors =  jQuery.parseJSON(jqXHR.responseText);
			        $(".error").html($errors.errors);
			    },
			    contentType: false,
			    processData: false
			});
		});
	});
</script> -->
<script>
	$(document).ready(function(){
		$('#resume').change(function(){
			$("#inputfile").prop("disabled", $(this).is(':checked'));
		});
		$('#resetbutton').click(function(event) {
			$("#inputfile").prop("disabled", null);
		});
	});
</script>