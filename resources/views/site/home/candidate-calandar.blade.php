@extends('site.layouts.master-dashboard')

@section('content')
<input type="hidden" id="pending_id" value="{{$job_details->id}}">
<section>
	<div class="container-fluid">
		<div class="container container-whitebg">
			<div class="row">
				<div class="col-lg-12 text-center">
					<h3 class=" ">Confirming Interview</h3>
				</div>
			</div>
			<div class="spacing">&nbsp;</div>
			<div class="row">
				<div class="col-xs-12 col-md-12 mainDiv text-center">
					<h6 class="roleInterviewing">What role are you interviewing for?</h6>
					<h4>{{$job_details->job_title}} - {{$job_details->suburb}}</h4>            
			 
			 		@if(!empty($job_details->interviewer_name))
						<h6 class=" ">Who will you be interviewing?</h6>
						<div class="col-md-4">&nbsp;</div>
						<div class="col-xs-12 col-sm-10 col-md-4 ">
							<h4>{{$job_details->interviewer_name}} </h4>
							<p>{{$job_details->interviewer_title}} </p>
						</div>
					@endif
						<div class="col-md-4">&nbsp;</div>
					 
					<h6 class="selectTime">Select a time</h6>
				</div>
				<div class="col-xs-12 col-sm-12 col-md-12 select-time">
					<div class="col-xs-12 col-sm-6 col-md-4">
						<div id="calanda-interview" data-toggle="calendar"></div>

						<div class = "ibox"></div>
						<label>
							Book Interviews
						</label>
						<div class = "ibox1"></div>	 
						<label class = "iphone5">
							N/A
						</label>
					</div>
					<div class="col-xs-12 col-sm-6 col-md-7 paddingNone maincc">
						<div class="col-xs-12 col-sm-12 col-md-12 paddingRightside">
							<h5 class="sTime pull-left">Selected a time</h5>
							<span class="pull-right">Eastern - Sydney<br><a href="#">Change time zone</a></span>
						</div>

						<div class="col-xs-12 col-sm-12 col-md-12 paddingNone">
							<div class="col-xs-12 col-sm-12 col-md-12 paddingNone timeslotdiv">

								<?php
								$timefrm =$current =  date("H:i", strtotime("2000-01-01 08:00"));


								
								$current = $timeto = date("H:i", strtotime("+".$job_details->time." minutes", strtotime($timefrm) ));
								

								for($i=0 ;$i<22 ;   $i++)
								{
									?>
									<div class="col-xs-6 col-sm-4 col-md-3   paddingRightside timeslot"  id="time-slot" data-time='{{$timefrm}}-{{$timeto}}'>
										<h4 class="timeIn hoover">{{$timefrm}}-{{$timeto}}<span class="meridian"> </span></h4></div>
										<?php

										$timefrm = date("H:i", strtotime("+30 minutes", strtotime($timefrm) ));
										$timeto = date("H:i", strtotime("+30 minutes", strtotime($timeto) ));
										


									} ?>

								</div>
							</div>

						</div>

					</div>

					<div class="spacing">&nbsp;</div>
					<div class="col-md-12 text-center">
						<h6 class="yourIntervied">Your Interview Details</h6>
						<h4 class="seniorCredit">{{$job_details->interviewer_name}}</h4>
						<h5 class="seniorCredit">{{$job_details->interviewer_title}}</h5>

						@if(isset($job_candidate->comments))
							<h5 class="yourIntervied" style="font-weight: bold;">Address and Instructions: </h5>
							<h5 class="yourIntervied">{{$job_candidate->comments}} </h5>
						@endif

						@if(isset($job_candidate->panel_member))
							<h5 class="yourIntervied" style="font-weight: bold;">Convenor/Panel Member: </h5>
							<h5 class="yourIntervied">{{$job_candidate->panel_member}} </h5>
						@endif


						 


						<h4 class="monthDate"></h4> 
						<div class="failure_interview" style="color:red">
						</div>  
						<form id="acpt-form" method="post" class=" form-signin form-horizontal">
							<input type="hidden" name="_token" id="token" value="{{{csrf_token() }}}">
							<input type="hidden" id="interview_id" name="interview_id" value="">
							<input type="hidden" id="interview_pending_date_id" name="interview_pending_date_id" value="{{$job_details->id}}">
							<button type="button" id="reject-inter" class="btn btn-primary confirmInterview" style="background-color: #b9001a;">REJECT INTERVIEW</button>
							<button type="button" id="conf-inter" class="btn btn-primary confirmInterview">CONFIRM INTERVIEW</button>

						</div>
						<div id="rejectbox" >
							<div class="col-md-12 text-center" style="margin-left: 273px;margin-bottom: 20px;
							">
							<textarea class="col-md-6" rows="5" id="comment" name="comment"></textarea>
						</div>
						<div class="col-md-12 text-center" >
							<button type="button" id="send-mess" class="btn btn-primary confirmInterview">Reject Interview</button>
							<button type="button" id="cancel-back" class="btn btn-primary confirmInterview" style="background-color: #b9001a;">Cancel</button>
						</div>
					</div>
				</form>

			</div>
		</div>
	</div>
</section>
<script src="{{URL::to('/site/js/jquery.js')}}"></script>
<script type="text/javascript" src="{{URL::to('/site/js/time-calander.js')}}"></script>

@endsection