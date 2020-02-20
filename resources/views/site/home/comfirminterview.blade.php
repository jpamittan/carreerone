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
   
    @if(!empty($pending_interview_candidate))
        @include('site.partials.interviewdetails',array('pending_interview_candidate' => $pending_interview_candidate,'schedule_interview_candidate' => $schedule_interview_candidate))
    @endif
       
    <?php /*@include('site.partials.completedinterview',array('apply_history' => $apply_history))*/ ?>

    @include('site.partials.interview-future-jobs',array('insjobs' => $insalljobs))
    @include('site.partials.interviewconfirm',array('apply_history' => $apply_history))
         
    <?php /*@include('site.partials.jobapplyhistory',array('completed_interview_candidate' => $completed_interview_candidate))*/ ?>

	</div>
		 @include('site.partials.dahboardside',array('candidate_interview' => $candidate_interview))
    </div>
    </div>

</body>
</html>
@endsection
