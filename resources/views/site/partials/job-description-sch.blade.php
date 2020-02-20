@if(!empty($job->rd_url))
<div class="col-xs-12 col-sm-12 col-md-12">
	<p class= "jobdecription"><b>Download role description  
	<a href="{{URL::to('/site/download_rd/'.$job->rd_id )}}" id="download">
                               Click Here</a></b></p>
</div>
@endif

<?php /*									
									<div class="col-xs-12 col-sm-12 col-md-12 desc-padd">
									<div class="col-xs-12 col-sm-12 col-md-12 desc-padd">
									<p class = "jobdecription"> 
										{!! nl2br(e($job->position_description)) !!}
									</p>
									
									</div>
									<div class="col-xs-12 col-sm-12 col-md-12">
										<p class= "jobdecription">To see full Role Description please <a class="roledecription" href="#">Click Here</a></p>
									</div>
									<div id="desc" class="col-xs-12 col-sm-12 col-md-12 desc-padd">
									@if(!empty($job->role_description))

										<p class= "jobdecription">{!! nl2br(e($job->role_description)) !!}</p>
									
									@endif
									</div>
									</div>
									
	<script>
  $(document).ready(function() {
  	$('#desc').hide();
     $('.roledecription').click(function() {
    $('#desc').toggle();
    });
  });


</script>
*/ ?>