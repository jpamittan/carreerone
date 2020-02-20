@extends('site.layouts.master-dashboard')

@section('content')

    <div class="container container-whitebg1">
    <div class="row">
	<div class="col-md-9 dbmaindas">
    <!-- success -->
    @if(!empty(\Session::get('message')))
    <div class="alert alert-success">
       {{\Session::get('message')}}
        </div>
    @endif

 
 
   <!-- /success -->    
        @include('site.partials.jobs-display', array('jobs' => $jobs,'job_status' =>$job_status,'job_latest' =>$job_latest ,'careeronejobs' => $careeronealljobs ,'insjobs' => $insalljobs ,'insmatchedjobs' => $insmatchedjobs , 'inshistoryalljobs' => $inshistoryalljobs , 'insfuturealljobs' => $insfuturealljobs))
<?php /*     @include('site.partials.jobapplyhistory',array('jobs' => $jobs,'job_status' =>$job_status,'apply_history' => $apply_history,'job_latest' =>$job_latest))  */ ?>
	</div>
		 @include('site.partials.dahboardside',array('candidate_interview' => $candidate_interview, 'rss_feeds' => $rss_feeds))
    </div>
    </div>

</body>

</html>
						
@endsection
