 <style>
 .ins .can-marg{
    margin-left:0px;
 }

 #skill-screened-outer {
    position: fixed;
    background: rgba(0,0,0,0.7);
    top: 0px;
    left: 0px;
    right: 0px;
    bottom: 0px;
    z-index: 1000000;
    display: none;
}

.skill-screened {
    padding: 45px;
    background: white;
    position: fixed;
    left: 50%;
    top: 50%;
    transform: translate3d(-50%, -50%, 0);
    z-index: 1000001;
    border-radius: 5px;
}

.skill-screened h4 {
    text-align: center;
    color: #006FB0;
    margin-top: 0px;
    font-weight: bold;
}

.skill-screened p {
    font-size: 12px;
    margin-bottom: 32px;
    padding: 0px 10px;
}

.skill-screened input, .skill-screened select, .skill-screened textarea {
    width: 100%;
    border: 1px solid #E1E1E1;
    border-radius: 5px;
    padding: 8px 10px;
    margin-bottom: 10px;
}

.skill-screened input, .skill-screened textarea {
    padding-left: 14px;
}

.skill-screened textarea {
    height: 125px;
}

.submit-screened {
    float: right;
}

 

.submit-screened button {
    width: 145px;
    background-color: #006FB0;
    border-radius: 5px;
    border: none;
    color: white;
    padding: 10px;
}

.skill-screened .close-screened {
    position: absolute;
    top: 20px;
    right: 20px;
    color: #454545;
    cursor: pointer;
    font-size: 16px;
    font-weight: bold;
}
 

 </style>
<section>
	<div class="container-fluid ">
		<div class="container container-whitebg" style="margin-top:-50px;">
			<div class="row">
				<div class="col-lg-12 text-center">
					<h2 class=" ">Mobility Assessment Interview Scheduler</h2>
				</div>
			</div>
			<div class="spacing">&nbsp;</div>
			<div class="row">

				<div class="col-xs-12 col-md-12 mainDiv text-center">
					<h3 class=" ">Role Details</h3>
					<h4>{{$job->job_title}} </h4>            
					<h5>
            @if(!empty($job->job_grade))
              Grade - {{$job->job_grade}}
            @endif
            ${{number_format($job->salary_from)}} - ${{number_format($job->salary_to)}}

            </h6>
					<h5><b> {{$job->agency_name}}</b></h6>
					 
			@if(!empty($candidates))
					<div class="spacing">&nbsp;</div>
					<div class="col-xs-12 col-md-12 mainDiv" style="text-align:left; ">
					<h5> Please confirm the candidates you wish to interview and the dates and times you have available.
<br>
Please select which candidates you wish to interview by clicking ‘Interview’. If you do not wish to interview a candidate click ‘Screen” but you will need to provide a brief comment on why they are not suitable.</h5>
					</div>
					<div class="spacing">&nbsp;</div>
					

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12 jobmatchYout paddingNone">
                    
                   
                    <div class="col-md-12 col-sm-12  col-xs-12  intervconfirm inconfirmmarg">
                    <form id="sch-form" method="post">
                     <input type="hidden" id="token" name="_token" value="{{{csrf_token()}}}">
                      <input type="hidden" class="jobid" name="job_id" value="{{$job->id}}">
                       <div class="col-md-12 col-sm-12  col-xs-12 sche-paddingnone">
                        
                       @foreach($candidates as $candidate)
                        
                                    <div class="col-md-4 col-sm-4  col-xs-12 sche-paddingmarg border-rec" schedule_div_id="{{$candidate->ins_job_candidateid}}">
                                        <div class="col-md-12 col-sm-12  col-xs-12 paddind-recruiter ">
                                             
                                          <div class="col-md-7 col-sm-7  col-xs-7 paddind-recruiter ">
                                              <h4 class="pull-left can-marg" id="candi_name_{{$candidate->ins_job_candidateid}}">{{$candidate->first_name}} {{$candidate->last_name}}</h4>
                                          </div>
                                          @if(!empty($candidate->resumeID ))
                                               <div class="col-md-5 col-sm-5  col-xs-5 paddind-recruiter " style="margin-top:5px;">
                                                  <a href="{{URL::to('/site/download_resume/'.$candidate->resumeID )}}" id="download">
                                            Download resume</a>
                                              </div>
                                          @endif
                                        </div>
                                        
                                        
                                        <div class="col-md-12 col-sm-12  col-xs-12   paddind-recruiter " style="text-align: left; padding-bottom: 10px;">
                                            <div class="col-md-3 col-sm-3 col-xs-3  " style="padding-left:0px;" >
                                               <input type="radio" class="candi schedule_candi_radio" name="candidates_{{$candidate->candidate_id}}[]" value="{{$candidate->candidate_id}}" ins-id-yes="{{$candidate->ins_job_candidateid}}"  checked="checked">&nbsp;&nbsp; Interview
                                             </div>
                                             <div class="col-md-3 col-sm-3 col-xs-3"  >
                                               <input type="radio" class="candi schedule_candi_radio screened-candidates" name="candidates_{{$candidate->candidate_id}}[]" value="no" ins-id-no="{{$candidate->ins_job_candidateid}}"  ins-canid-no="{{$candidate->candidate_id}}"  >&nbsp;&nbsp; Screen
                                             </div>
                                        </div>

                                        <!-- Popup NO -->
                                          <div id="skill-screened-outer" class="skill-screened-outer_{{$candidate->ins_job_candidateid}}">
                                                <div class="skill-screened">
                                                    <div class="close-screened"  ins-id-close="{{$candidate->ins_job_candidateid}}">X</div>
                                                    <h4 id='pop_candidate_name'>{{$candidate->first_name}} {{$candidate->last_name}} </h4>
                                                    <p>Please provide a brief comment on why this candidate is not suitable. This will be used to advise

                                            the candidate as to why they have not secured an interview. Though, you will need to complete a

                                            Mobility Suitability Assessment Report for all candidates at the end of this process.</p>
                                                     
                                                          <input type="hidden" id="hidden-ins-id" name="hidden-ins-id" value="{{$candidate->ins_job_candidateid}}">
                                                        <div>
                                                            <textarea id="screened_description_{{$candidate->ins_job_candidateid}}" name="screened_description" placeholder="Feedback" maxlength="2000"></textarea>
                                                        </div>
                                                        <div class="submit-screened" ins-id-submit="{{$candidate->ins_job_candidateid}}">
                                                            <button type="button"   class="submit-screened-btn" >Save</button>
                                                        </div>
                                                       
                                                    
                                                </div>
                                            </div>
                                      <!-- END Popup NO -->


                                          
                                    </div>
                                   
                           @endforeach

                       </div> 
                      
                      </form>
                      
                    </div>
 
                  
                </div>
            </div>


            
            <div class="spacing">&nbsp;</div>

            <div class="col-xs-12 col-md-12 mainDiv" style="text-align:left; ">
          <h5> Below you can select the interview length from the drop down and then click timeslots to mark

them as available to candidates. These will then be sent to the candidates and selected on a first

come first served basis. You will be notified of the confirmed interview times once they have

been selected.</h5>
          </div>




					<div class="col-xs-12 col-sm-12 col-md-12 select-time">
						<div class="col-xs-12 col-sm-6 col-md-4">
							<div id="glob-data" data-toggle="calendar"></div>
						</div>
						
						<div class="col-xs-12 col-sm-6 col-md-7 paddingNone maincc">
							<div class="col-xs-12 col-sm-12 col-md-12 paddingRightside">
								<h5 class="sTime pull-left">Selected a time</h5>

								<span class="pull-left select-time-marg"><select class="form-control time" id="sel1" class="time">
									<option value="30">30</option>
									<option value="60">60</option>
									<option value="90">90</option>
									<option value="120">120</option>
								</select>
							</span>
						</div>
						<div class="col-xs-12 col-sm-12 col-md-12 paddingNone">
							<div class="col-xs-12 col-sm-12 col-md-12 paddingNone timeslotdiv">
								<?php
								$timefrm =$current =  date("H:i", strtotime("2000-01-01 08:00"));
								$current = $timeto = date("H:i", strtotime("+30 minutes", strtotime($timefrm) ));
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
       <div class="failure_interview" style="color:red">
						</div>
          <div class="  class='col-md-12" >
            
            <div id="rejectbox"  class='col-md-6' style="text-align: center;" >
                <br>
                <textarea class=" textarea" rows="5" cols="70" id="comments" name="comments" placeholder='Address and Instructions'></textarea>
            </div>

            <div id="rejectbox1"  class='col-md-6'  style="text-align: center;" >
                <br>
                <textarea class="textarea" rows="5" cols="70" id="comments_panelmember" name="comments_panelmember"  placeholder='Convenor/Panel Members'></textarea>
            </div>

      </div>

       <div class="spacing">&nbsp;</div>
				<div class="col-md-12 text-center">
						<button type="button" class="btn btn-primary confirmInterview">SCHEDULE INTERVIEW</button>
						
					</div>
		
		 @else
		
		<div class="col-md-12 col-sm-12 col-xs-12 text-center">
		 <h3 style="color:green">No candidate found to schedule the interview for the above job</h3>
		 </div>
		@endif
			</div>
		</div>
		<script type="text/javascript" src="{{URL::to('/site/js/schedule-interview.js')}}"></script>

	</section>

<?php
/*

  <div id="skill-screened-outer">
    <div class="skill-screened">
        <div class="close-screened">X</div>
        <h4 id='pop_candidate_name'> </h4>
        <p>Please provide a brief comment on why this candidate is not suitable. This will be used to advise

the candidate as to why they have not secured an interview. Though, you will need to complete a

Mobility Suitability Assessment Report for all candidates at the end of this process.</p>
        <form class="screened-candidate" method="post" action="{{URL::route('site-suggest-skill')}}">
              <input type="hidden" id="hidden-ins-id" name="hidden-ins-id" value="">
            <div>
                <textarea id="screened_description" name="screened_description" placeholder="Description" maxlength="2000"></textarea>
            </div>
            <div class="submit-screened">
                <button type="submit" name="submit" class="submit-screened-btn">Submit</button>
            </div>
            <input type="hidden" id="token" name="_token" value="{{{csrf_token() }}}">
        </form>
    </div>
</div>

*/ ?>
