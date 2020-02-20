 <?php use App\Models\Factories\Format; ?>
<div class="col-xs-12 col-sm-12 col-md-12 portfolio">
									<h4 class="risk">{{$job->job_title}}</h4>
								</div>
								<div class="col-xs-12 col-sm-12 col-md-12 ">
									<div class="col-xs-12 col-sm-8 col-md-10 detailsmargin">
									<div class="col-xs-6 col-sm-4 col-md-4">
											<h6 class ="marginh6">Vacancy reference</h6>
										</div>
										<div class="col-xs-6 col-sm-8 col-md-6">
											<h6 class ="marginh6 tit-minheight">
											@if(!empty($job->vacancy_reference_id)){{ $job->vacancy_reference_id }} @else&nbsp;@endif
											 </h6>
										</div>


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
									
								</div>



   