
<section>
	<div class="container-fluid ">
		<div class="container container-whitebg" style="margin-top:-50px;">
			<div class="row">
				<div class="col-lg-12 text-center">
					<h2 class=" ">Screening</h2>
				</div>
			</div>
			<div class="spacing">&nbsp;</div>
			<div class="row">

				<div class="col-xs-12 col-md-12 mainDiv text-center">
					<h3 class=" ">Job Details</h3>
					<h4>{{$job->job_title}} - {{$job->location}}</h4>            
					<h5>${{number_format($job->salary_from)}} - ${{number_format($job->salary_to)}}</h6>
					<h5><b>Agency - {{$job->agency_name}}</b></h6>
					<h5>Grade - {{$job->job_grade}}</h6>
					@if(!empty($candidates))
					<div class="spacing">&nbsp;</div>
					<div class="col-xs-12 col-md-12 mainDiv" style="text-align:; ">
					<h5>"Select EiT from the below list and provide brief comment on why each of these EiT screened" </h5>
					</div>
					<div class="spacing">&nbsp;</div>
					

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12 jobmatchYout paddingNone">
                    <div class="col-md-12 col-sm-12  col-xs-12  ">
                        <h3 class="">Candidates</h3>
                        <!-- <h6 class="appliedJobs">You have applied for 1 jobs in the last 18 months</h6> -->

                    </div>
                 
                   
                    <div class="col-md-12 col-sm-12  col-xs-12  intervconfirm inconfirmmarg">
                    <form id="sch-form-screening" method="post">
                     <input type="hidden" id="token" name="_token" value="{{{csrf_token()}}}">
                      <input type="hidden" class="jobid" name="job_id" value="{{$job->id}}">
                       <div class="col-md-12 col-sm-12  col-xs-12 sche-paddingnone">
                        
                       @foreach($candidates as $candidate)
                       @if($canddates_scheduled !=0)
                       @foreach($canddates_scheduled as $canddates_sch)
                         @if(($canddates_sch->candidate_id != $candidate->candidate_id))
                     
                            <div class="col-md-4 col-sm-4  col-xs-12 sche-paddingmarg border-rec" style="text-align: left;padding-top:15px; padding-bottom:10px;min-height :100px;">
                                <div class="col-md-12 col-sm-12  col-xs-12 paddind-recruiter ">
                                     <div class="col-md-2 col-sm-2  col-xs-2 paddind-recruiter ">
                                   
                                  <input type="checkbox" class="candi screening_candi_checkbox" name="candidates_screening[]" value="{{$candidate->candidate_id}}" ins-id-screening="{{$candidate->ins_job_candidateid}}">
                                  </div>
                                  <div class="col-md-10 col-sm-10  col-xs-10 paddind-recruiter ">
                                  <h4 class="pull-left can-marg">{{$candidate->first_name}} {{$candidate->last_name}}</h4>
                                </div>
                                </div>
                               
                                 @if(!empty($candidate->resume_name)) 
                                      <div class=" margprof-bott1">
                                       Resume Name :  <a href="{{URL::to('/site/download_resume/'.$candidate->resumeID )}}" id="download">
                                      {{$candidate->resume_name}}</a> 
                                      </div>
                                  @endif
                                 <?php /*

                                @if(!empty($candidate->covering_letter_name)) 

                                  <div class="col-md-6 col-sm-6  col-xs-6 margprof-bott1">
                                     <h5 class="pull-left">Covering Letter</h5>
                                  </div>
                                  <div class="col-md-6 col-sm-6  col-xs-6 margprof-bott1 ">
                                  <h5 class="pull-left">&nbsp; <a href="{{URL::to('/site/download_cvletter/'.$candidate->coverLettID )}}" id="download">{{$candidate->covering_letter_name}}</a></h5>
                                  </div>
                                @endif
                                  @if(!empty($candidate->sup_id)) 
                                       <div class="col-md-6 col-sm-6  col-xs-6 margprof-bott1">
                                        <h5 class="pull-left">Supporting Docs</h5>
                                      </div>
                                      <div class="col-md-6 col-sm-6  col-xs-6 margprof-bott1">
                                       <h5 class="pull-left">&nbsp;<a href="{{URL::to('/site/download_suppdf/'.$candidate->coverLettID )}}" id="download">{{$candidate->sup_id}}</a></h5>
                                      </div>
                                   @endif
                              */ ?>
                            </div>



                            @endif
                            @endforeach
                            @else
                            <div class="col-md-4 col-sm-4  col-xs-12 sche-paddingmarg border-rec" screening_div_id="{{$candidate->ins_job_candidateid}}">
                                <div class="col-md-12 col-sm-12  col-xs-12 paddind-recruiter ">
                                     <div class="col-md-2 col-sm-2  col-xs-2 paddind-recruiter ">
                                  <input type="checkbox" class="candi screening_candi_checkbox" name="candidates_screening[]" value="{{$candidate->candidate_id}}" ins-id-screening="{{$candidate->ins_job_candidateid}}">
                                  </div>
                                  <div class="col-md-10 col-sm-10  col-xs-10 paddind-recruiter ">
                                  <h4 class="pull-left can-marg">{{$candidate->first_name}} {{$candidate->last_name}}</h4>
                                </div>
                                </div>
                                
                                
                                <div class="col-md-5 col-sm-6  col-xs-6 margprof-bott1">
                                <h5 class="pull-left">Resume Name</h5>
                                </div>
                                <div class="col-md-7 col-sm-6  col-xs-6 margprof-bott1 ">
                                <h5 class="pull-left">&nbsp; <a href="{{URL::to('/site/download_resume/'.$candidate->resumeID )}}" id="download">
                                {{$candidate->resume_name}}</a></h5>
                                </div>


                                 @if(!empty($candidate->covering_letter_name)) 

                                    <div class="col-md-6 col-sm-6  col-xs-6 margprof-bott1">
                                       <h5 class="pull-left">Covering Letter</h5>
                                    </div>
                                    <div class="col-md-6 col-sm-6  col-xs-6 margprof-bott1 ">
                                    <h5 class="pull-left">&nbsp; <a href="{{URL::to('/site/download_cvletter/'.$candidate->coverLettID )}}" id="download">{{$candidate->covering_letter_name}}</a></h5>
                                    </div>
                                @endif

                                 @if(!empty($candidate->sup_id)) 

                                     <div class="col-md-6 col-sm-6  col-xs-6 margprof-bott1">
                                      <h5 class="pull-left">Supporting Docs</h5>
                                    </div>
                                    <div class="col-md-6 col-sm-6  col-xs-6 margprof-bott1">
                                     <h5 class="pull-left">&nbsp;<a href="{{URL::to('/site/download_suppdf/'.$candidate->coverLettID )}}" id="download">{{$candidate->sup_id}}</a></h5>
                                    </div>
                                  @endif
                            </div>
                            @endif
                           @endforeach

                       </div> 
                      
                      </form>
                      
                    </div>
                  
                    <!-- <div class="col-md-12 col-sm-12  col-xs-12  intervconfirm">
                    <h4 style="colorred">No Scheduled Interview</h4>
                    </div> -->
                  
                </div>
            </div>




       <div class="spacing">&nbsp;</div>
       <div class="failure_interview" style="color:red">
       </div>
            <div id="rejectbox"  style="text-align: center;" >
              <label>Screening Comments</label><BR>
                <textarea class="" rows="5" cols="70" id="screening_comments" name="screening_comments"></textarea>
            </div>
       <div class="spacing">&nbsp;</div>
        <div class="col-md-12 text-center">
            <button type="button" class="btn btn-primary confirmInterviewScreening">Screened Candidates</button>
            
          </div>

           
 
		
		 @else
		
		<div class="col-md-12 col-sm-12 col-xs-12 text-center">
		 <h3 style="color:green">No candidate found for screening</h3>
		 </div>
		@endif
			</div>
		</div>
		 

	</section>
