@if(!empty($job->rd_url))
<div class="col-xs-12 col-sm-12 col-md-12">
	<p class= "jobdecription"><b>Download role description 
	<a href="{{URL::to('/site/download_rd/'.$job->rd_id )}}" id="download">
                               Click Here</a></b></p>
</div>
@endif


@if(!empty($job->advert))
<div class="col-xs-12 col-sm-12 col-md-12">
<h4>Advert</h4>
{!! html_entity_decode( nl2br(e($job->advert)) )  !!}
</div>
@endif

 


<?php /*
<div class="col-xs-12 col-sm-12 col-md-12 desc-padd">
	<p class = "jobdecription"> 
	{!! nl2br( ($job->position_description)) !!} 
		
	</p>
</div>
@if(!empty($job->role_description))
<div class="col-xs-12 col-sm-12 col-md-12">
	<p class= "jobdecription"><b>To see full job Description please <a class="roledecription" href="#">Click Here</a></b></p>
</div>

	<div id="desc" class="col-xs-12 col-sm-12 col-md-12 desc-padd">
		<p class= "jobdecription">{!! nl2br( ($job->role_description)) !!}</p>
	
</div>
@endif

	<div class="col-md-12 text-center hidden2 ">
		@if(empty($job->job_apply)) 
			<button  id="descapplybutton" class="btn btn-primary apply2">I'm interested</button>
		@endif
	</div>
*/
?>
<script>
$(document).ready(function() {
	$('#desc').hide();
	$('#descapplybutton').click(function() {
		window.location.href = '{{URL::route('site-job-apply',['job_id' => $job->id])}}';
		return true;
	});
	$('.roledecription').click(function() {
		$('#desc').toggle();
	});
});
</script>