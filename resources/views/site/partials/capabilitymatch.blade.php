<div class="col-xs-12 col-sm-12 col-md-12  jobHeader jobHeader1 jobtablet">
	<div class="col-xs-12 col-sm-12 col-md-12 paddingtabright">
		<h4> Your Capability match</h4>
		@if(!empty($capabilities))
		@foreach($capabilities as $key => $value)
		<div class="col-xs-12 col-sm-12 col-md-12 mainCapapuity tablet">
			<div class="col-md-2">
				<img alt="jobmatch" src="{{$value[0]['image']}}" class="img-responsive">
				<h5 class="perHead">{{$key}}</h5>
			</div>
				<div class="col-xs-12 col-sm-12 col-md-10 nameMain capabulityDiv">
					@foreach($value as $match)
					<div class="col-xs-8 col-sm-7 col-md-7 capabulityDivName">
						<h6 class="joNames"> 
							{{$match['capabilities']}}
						</h6>
					</div>
					@if($match['core_status'] == 1)
					<div class="col-xs-3 col-sm-4 col-md-4">
						<h6 class="accepted1"><b>{{$match['level']}}</b></h6>
					</div>
					@else
					<div class="col-xs-3 col-sm-4 col-md-4">
						<h6 class="accepted">{{$match['level']}}</h6>
					</div>
					@endif
					@if($match['score'] == 0.5)
					<div class="col-xs-1 col-sm-1  col-md-1">
						<img alt="correct" src="/site/img/correct.jpg" class="img-responsive tickCorrect img1">
					</div>
					@else
					<div class="col-xs-1  col-sm-1 col-md-1">
						<span class="glyphicon glyphicon-minus minuscl"></span>
					</div>
					@endif
			@endforeach
			</div>
		</div>
		@endforeach
		@else
		<div class="col-xs-12 col-sm-12 col-md-12 mainCapapuity tablet">
		<h5 style="color:red">Your Capability Match is Not Yet Done....</h5>
		</div>
		@endif
	</div>

	<div class="col-xs-12 col-sm-12 col-md-12 jobHeader">
		<div class="col-md-12">
			@if(!empty($skill_match))

				<h4>Your Skill Match</h4>
			
				@foreach($skill_match as $skill)
						<div class="col-xs-12 col-sm-12 col-md-12 nameMain">
							<div class="col-xs-10 col-md-10">
								<h6 class="joNames">{{$skill->skill_name}}</h6>
							</div>
							@if($skill->status == 1)
								<div class="col-xs-2 col-md-2">
									<img alt="correct" src="/site/img/correct.jpg" class="img-responsive tickCorrect" style="width: 52%;">
								</div>
							@else
								<div class="col-xs-2  col-sm-2 col-md-2">
						<span class="glyphicon glyphicon-minus minusc3"></span>
					</div>
							@endif
						</div>
				@endforeach
			@else
			<h4>Your Skill match</h4>

			<div class="col-xs-12 col-md-12" style="margin-bottom: 20px;">
					<h5 class="joNames" style="color: #f15151;">
						<b>No Skill Match Done For This Job</b>
					</h5>
				</div>
			@endif
		</div>
	</div>


</div>