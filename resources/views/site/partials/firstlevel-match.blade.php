<div class="col-sm-12 col-md-6 jobHeader">
										<div class="col-md-12">
											<img alt="jobmatch" src="/site/img/jobmatch.jpg" class="img-responsive jobImg">
											<h4>JobMatch</h4>
											<div class="col-xs-12 col-sm-12 col-md-12 nameMain">
												<div class="col-xs-10 col-md-10">
													<h6 class="joNames">{{$job_det->location}}</h6>
												</div>
												@if(!empty($job_match->status) && $job_match->match_status == 121660000)
												<div class="col-xs-2 col-md-2">
													<img alt="correct" src="/site/img/correct.jpg" class="img-responsive tickCorrect" style="width: 43%;">
												</div>
												@else
												<div class="col-xs-2 col-md-2">
													<span class="glyphicon glyphicon-minus minuscl"></span>
												</div>
												@endif
											</div>
											<div class="col-xs-12 col-sm-12 col-md-12 nameMain">
												<div class="col-xs-10 col-md-10">
													<h6 class="joNames">${{number_format($job_det->salary_from)}} - ${{number_format($job_det->salary_to)}}</h6>
												</div>
												@if(!empty($job_match->status) && $job_match->match_status == 121660000)
												<div class="col-xs-2 col-md-2">
													<img alt="correct" src="/site/img/correct.jpg" class="img-responsive tickCorrect" style="width: 43%;">
												</div>
												@else
												<div class="col-xs-2 col-md-2">
													<span class="glyphicon glyphicon-minus minuscl"></span>
												</div>
												@endif
											</div>
											<div class="col-xs-12 col-sm-12 col-md-12 nameMain">
												<div class="col-xs-10 col-md-10">
													<h6 class="joNames">{{$job_det->category_name}}</h6>
												</div>
												@if(!empty($job_match->status) && $job_match->match_status == 121660000)
												<div class="col-xs-2 col-md-2">
													<img alt="correct" src="/site/img/correct.jpg" class="img-responsive tickCorrect" style="width: 43%;">
												</div>
												@else
												<div class="col-xs-2 col-md-2">
													<span class="glyphicon glyphicon-minus minuscl"></span>
												</div>
												@endif
											</div>
										</div>
										
									</div>