<section class="applySection">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12 jobmatchYout paddingNone">
                    <div class="col-md-12 col-sm-12  col-xs-12  sort">
                        <h3 class="">List Of Candidates</h3>
                        <!-- <h6 class="appliedJobs">You have applied for 1 jobs in the last 18 months</h6> -->

                    </div>
                 
                   
                    <div class="col-md-12 col-sm-12  col-xs-12  intervconfirm inconfirmmarg">
                       <div class="col-md-12 col-sm-12  col-xs-12 sche-paddingnone">
                       @foreach($candidates as $candidate)
                            <div class="col-md-4 col-sm-4  col-xs-12 sche-paddingmarg border-rec">
                                <div class="col-md-12 col-sm-12  col-xs-12 paddind-recruiter ">
                                  <input type="checkbox" name="candidates" value="{{$candidate->candidate_id}}">{{$candidate->first_name}} {{$candidate->last_name}}
                                </div>
                                <div class="col-md-6 col-sm-6  col-xs-6">
                                    <b>Title</b>
                                </div>
                                <div class="col-md-6 col-sm-6  col-xs-6 margprof-bott1">
                              &nbsp; {{$candidate->title}}
                                </div>
                                <div class="col-md-6 col-sm-6  col-xs-6">
                                    <b>Job Grade</b>
                                </div>
                                <div class="col-md-6 col-sm-6  col-xs-6 margprof-bott1">
                               &nbsp; {{$candidate->job_grade}}
                                </div>
                                 <div class="col-md-6 col-sm-6  col-xs-6">
                                    <b>Agency Name</b>
                                </div>
                                <div class="col-md-6 col-sm-6  col-xs-6 margprof-bott1">
                              &nbsp; DELL
                                </div>
                                <div class="col-md-6 col-sm-6  col-xs-6 paddind-recru">
                                    <b>Resume Name </b>
                                </div>
                                <div class="col-md-6 col-sm-6  col-xs-6 margprof-bott1 paddind-recru">
                              &nbsp; {{$candidate->resume_name}}
                                </div>
                                <div class="col-md-6 col-sm-6  col-xs-6 paddind-recru">
                                    <b>Covering Letter </b>
                                </div>
                                <div class="col-md-6 col-sm-6  col-xs-6 margprof-bott1 paddind-recru">
                               &nbsp;{{$candidate->covering_letter_name}}
                                </div>
                                 <div class="col-md-6 col-sm-6  col-xs-6 paddind-recru">
                                    <b>Supporting Docs </b>
                                </div>
                                <div class="col-md-6 col-sm-6  col-xs-6 margprof-bott1 paddind-recru">
                              &nbsp; {{$candidate->sup_name}}
                                </div>
                            </div>
                            
                           @endforeach

                       </div> 
                      
                      
                      
                    </div>
                  
                    <!-- <div class="col-md-12 col-sm-12  col-xs-12  intervconfirm">
                    <h4 style="colorred">No Scheduled Interview</h4>
                    </div> -->
                  
                </div>
            </div>
        </section>