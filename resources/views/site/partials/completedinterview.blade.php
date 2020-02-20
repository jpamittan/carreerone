
<section class="applySection">
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12 jobmatchYout paddingNone">
                    <div class="col-md-12 col-sm-12  col-xs-12  sort">
                        <h3 class="">Completed Interview</h3>
                        <!-- <h6 class="appliedJobs">You have applied for 1 jobs in the last 18 months</h6> -->

                    </div>
                    @if(!empty($completed_interview_candidate))
                    @foreach($completed_interview_candidate as $completed)
                    <div class="col-md-12 col-sm-12  col-xs-12  intervconfirm inconfirmmarg">
                       <div class="col-md-12 col-sm-12  col-xs-12 sche-paddingnone">
                            <div class="col-md-6 col-sm-6  col-xs-12 com-paddingmarg">
                                <div class="col-md-6  col-sm-4  col-xs-6 sche-paddingnone">
                                    <b>Role Title</b>
                                </div>
                                <div class="col-md-6  col-sm-8  col-xs-6 sche-paddingnone">
                                 <a href ="{{URL::route('scheduled-details', ['job_id' => $completed->id])}}"> {{$completed->job_title}}</a>
                                   
                                </div>
                            </div> 
                           <?php /* <div class="col-md-6 col-sm-6  col-xs-12 com-paddingmarg">
                                <div class="col-md-5  col-sm-6 col-xs-6 sche-paddingnone">
                                    <b>Interview Status</b>
                                </div>
                                <div class="col-md-7  col-sm-6 col-xs-6 sche-paddingnone">
                                @if($completed->interview_status == 1)Selected @else Rejected @endif
                                </div>
                            </div>  */ ?>
                       </div> 
                        <div class="col-md-12 col-sm-12 col-xs-12 sche-paddingnone ">
                            <div class="col-md-6 col-sm-6 col-xs-12 com-paddingmarg">
                                <div class="col-md-6 col-sm-4 col-xs-6 sche-paddingnone">
                                    <b>Salary</b>
                                </div>
                                <div class="col-md-6 col-sm-8 col-xs-6 sche-paddingnone">
                                    ${{number_format($completed->salary_from)}} - ${{number_format($completed->salary_to)}}
                                </div>
                            </div> 
                           <div class="col-md-6 col-sm-6 col-xs-12 com-paddingmarg">
                                <div class="col-md-6 col-sm-6 col-xs-6 sche-paddingnone">
                                    <b>Hiring Manager Name</b>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-6 sche-paddingnone">
                                      {{$completed->prepared_by_name}}
                                </div>
                            </div> 
                       </div> 
                       <div class="col-md-12 col-sm-12 col-xs-12 sche-paddingnone ">
                            <div class="col-md-6 col-sm-6 col-xs-12 com-paddingmarg">
                                <div class="col-md-6 col-sm-4 col-xs-6 sche-paddingnone">
                                    <b>Agency</b>
                                </div>
                                <div class="col-md-6 col-sm-8 col-xs-6 sche-paddingnone">
                                  {{$completed->agency_name}}
                                </div>
                            </div> 
                           <div class="col-md-6 col-sm-6 col-xs-12 com-paddingmarg">
                                <div class="col-md-6 col-sm-6 col-xs-6 sche-paddingnone">
                                    <b>Hiring Manager Email</b>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-6 sche-paddingnone">
                                      {{$completed->prepared_by_email}}
                                </div>
                            </div> 
                       </div>
                       <div class="col-md-12 col-sm-12 col-xs-12  salamarg sche-paddingnone">
                           <div class="col-md-6 col-sm-6 col-xs-12 com-paddingmarg">
                                <div class="col-md-6 col-sm-6 col-xs-6 sche-paddingnone">
                                    <b>Hiring Manager Number</b>
                                </div>
                                <div class="col-md-6 col-sm-6 col-xs-6 sche-paddingnone">
                                      {{$completed->prepared_by_number}}
                                </div>
                            </div> 
                       </div>
                    </div>
                    @endforeach
                    @else
                      <div class="col-md-12 col-sm-12  col-xs-12 "  >
                        No Completed Interviews
                      </div>
                    @endif
                </div>
            </div>
        </section>

 

