 <?php use App\Models\Factories\Format; ?>
<div class="col-xs-12 col-sm-12 col-md-12 portfolio">
									<h4 class="risk">{{$job->job_title}}</h4>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-12 ">
									<div class="col-xs-12 col-sm-8 col-md-10 detailsmargin">
										<div class="col-xs-6 col-sm-4 col-md-4">
											<h6 class ="marginh6">Date Posted</h6>
										</div>
										<div class="col-xs-6 col-sm-8 col-md-6">
											<h6 class ="marginh6 tit-minheight">
 @if(!empty($job->appreoved_date)){{ Format::displayDate($job->appreoved_date  )  }} @else&nbsp;@endif

 </h6>
										</div>
										<div class="col-xs-6 col-sm-4 col-md-4">
											<h6 class ="marginh6">Expiry Date</h6>
										</div>
										<div class="col-xs-6 col-sm-8 col-md-6">
											<h6 class ="marginh6 tit-minheight">
											@if(!empty($job->deadline_date)){{ Format::displayDate($job->deadline_date  )  }} @else&nbsp;@endif
											 </h6>
										</div>
										<div class="col-xs-6 col-sm-4 col-md-4">
											<h6 class ="marginh6">Category</h6>
										</div>
										<div class="col-xs-6 col-sm-8 col-md-6">
											<h6 class = "boldfont marginh6 tit-minheight">{{$job->category_name}}</h6>
										</div>
										<div class="col-xs-6 col-sm-4 col-md-4">
											<h6 class ="marginh6">Agency</h6>
										</div>
										<div class="col-xs-6 col-sm-8 col-md-6">
											<h6 class = "boldfont marginh6 tit-minheight">{{$job->agency_name}}</h6>
										</div>
										<div class="col-xs-6 col-sm-4 col-md-4">
											<h6 class ="marginh6">Grade</h6>
										</div>
										<div class="col-xs-6 col-sm-8 col-md-6">
											<h6 class = "boldfont marginh6 tit-minheight">{{$job->job_grade}}</h6>
										</div>
										<div class="col-xs-6 col-sm-4 col-md-4">
											<h6 class ="marginh6 tit-minheight">Salary</h6>
										</div>
										<div class="col-xs-6 col-sm-8 col-md-6">
											<h6 class ="marginh6 tit-minheight">${{number_format($job->salary_from)}} - ${{number_format($job->salary_to)}}</h6>
										</div>

										 


									</div>
					 

<?php /*
									<div class="col-xs-4 col-sm-4 col-md-2 hidden1">
									@if(!empty($job->job_apply)) 
									<span class="applimarg" style=""><b>Applied</b></span>
									@else
										@if(!empty($job->job_draft)) 
											<span class=" " style="color:#ff0000; float:right;"><b>Draft</b></span>
										@endif
										<button id="applybutton1" class="btn btn-primary apply1 ">I'm interested</button>
										<form  id="ins-frm"  role="form" action="{{URL::route('site-job-reject',['job_id' => $job->id])}}" method="POST">
            								<input type="hidden" name="_token" id="token" value="{{{csrf_token() }}}">
											<button type="submit" id="rejectbutton1" class="btn btn-primary reject1 ">I'm not a suitable a match</button>
										</form>
									@endif
									</div>
*/ ?>


								</div>
	<script>
  $(document).ready(function() {
     $('#applybutton1').click(function() {
   		 window.location.href = '{{URL::route('site-job-apply',['job_id' => $job->id])}}';
    		return true;
    	});

     $('#iminterested').click(function(e) {
     	e.preventDefault();
 			$(this).html('Processing...');
			$(this).prop("disabled",true);
 

  		 	$.ajax({
                type: "GET",
                url: "/site/jobeoi/" +{{$job->id}},
                success: function (msg) {
                    if (msg.success == true) {
                    	 
                        window.location.href = '/site/getJobs/'+{{$job->id}}; 
                    } else {
                        
                    }
                },
                error: function () {
                	$(this).html("I'm interested");
                	$(this).prop("disabled",false);
                },
            });
    		 
    	});


 	$('#imnotinterested').click(function(e) {
     	e.preventDefault();
 		imnotinterested_txt =  ($('#imnotinterested_txt').val());
 		 
 		if(imnotinterested_txt == ''){
 			$('#imnotinterested_txt').addClass('alert alert-danger');
 			return false;
 		}else{
 			$('#imnotinterested').html('Processing...');
			$('#imnotinterested').prop("disabled",true);
			$('#imnotinterestedcancel').hide();
			 
			$('#imnotinterested_txt').removeClass('alert alert-danger');
			var formdata = $( "#rejected-form" ).serialize();
			$.ajax({
                method: "POST",
                  data:formdata,
                url: "/site/jobeoirejected",
                success: function (msg) {
                    if (msg.success == true) {
                    	 $('#myModal').hide();
                      //  window.location.href = '/site/getJobs/'+{{$job->id}}; 
                        window.location.href = '/site/dashboard'; 

                    } else {
                    	$('#imnotinterested').html('Submit');
						$('#imnotinterested').prop("disabled",false);
						$('#imnotinterestedcancel').show();
                    }
                },
                error: function () {
                	$('#imnotinterested').html('Submit');
					$('#imnotinterested').prop("disabled",false);
					$('#imnotinterestedcancel').show();

                },
            });


 		}

   		 	 
    		 
	});



     
  });

 
</script>


<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">
		<form  id="rejected-form"  role="form"  >
			<input type="hidden" name="_token" id="token" value="{{{csrf_token() }}}">
			<input type="hidden" name="job_id" id="job_id" value="{{$job->id}}">
						 							
		    <!-- Modal content-->
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal">&times;</button>
		        <h4 class="modal-title">Please enter reasons that you believe this Role is unsuitable </h4>
		      </div>
		      <div class="modal-body">
		        <p>
		        		<textarea id='imnotinterested_txt' name='imnotinterested_txt' style="width:100%; height:100px"></textarea>

		        </p>
		      </div>
		      <div class="modal-footer">
		        <button type="button" id="imnotinterestedcancel" class="btn btn-default" data-dismiss="modal">Cancel</button>
		         <button id="imnotinterested" class="btn btn-default"  >Submit</button>
		      </div>
		    </div>
	</form>
  </div>
</div>


   