 <?php use App\Models\Factories\Format; ?>
<?php
             
      $paginator =  $careeronejobs['paginator'];
      //print_r($paginator);
      $from= '';
      $to='';
      if(!empty($paginator)){
        $currentpage =$paginator->currentpage; 
        $perpage=$paginator->perpage; 
        $from= (($currentpage- 1) * $perpage ) +1;
        $to= $perpage * $currentpage;
        if($paginator->total < $to){
             $to= $paginator->total;
        }
      }
?> 

<section>
    <div class="row">
         <div class="col-xs-12 col-sm-12 col-md-12 jobmatchYout paddingNone">
            <div class="col-xs-12 col-sm-12 col-md-12 sort">
                <h3 class="col-sm-9 col-md-8 count_matches paddingNone pull-left">  {{$careeronejobs['params']['jobtype']}} {{$careeronejobs['jobCount']}}  found</h3> 

                @if(!empty($from))
                <h5 style="margin-top:25px;" class="pull-right">Showing {{$from}} to {{$to}}</h5>
                @endif
                <div class="col-sm-3 col-md-4 mostRelavant paddingNone">
                    
                    
                </div>
            </div>
            
        </div>
    </div>
</section> 
 @if(!empty($careeronejobs['jobs']))

         @foreach($careeronejobs['jobs'] as $job)
         

            <div  class="col-xs-12 col-sm-12 col-md-12  sort sort1 jobpadding">
                <div class="col-xs-12 col-sm-7 col-md-7 jobpadding">
                   <div class="col-xs-12 col-sm-12 col-md-12 jobpadding font-title">
                        <a href ="{{URL::route('site-get-jobs', ['job_id' => $job->id])}}">{{$job->job_title}}</a>
                        &nbsp;&nbsp;&nbsp;&nbsp;

                             @if(!empty($job->job_eoi_rejected)) 
                                <span class=" " style="color:red; float:right"   >Unmatched</span>
                                
                            @else
                                @if(empty($job->job_eoi)) 
                                        @if(!empty($job->isjobeoiapply)) 
                                            @if(!empty($job->job_apply)) 
                                             <span class=" "  style="color:green; float:right" >Applied</span>
                                            @elseif(!empty($job->job_draft)) 
                                                     <span class=" "  style="color:red; float:right" >
                                                         <a style="color:red; " href ="{{URL::route('site-get-jobs', ['job_id' => $job->id])}}">Draft</a>
                                                     </span>
                                            @else
                                               <span class=" "  style="color:green; float:right" >
                                                   <a style="color:green; " href ="{{URL::route('site-get-jobs', ['job_id' => $job->id])}}">Apply for role</a>
                                               </span>
                                            @endif
                                        @else
                                                 

                                            
                                            
                                        @endif

                                @else
                                    @if(!empty($job->job_eoi)  ) 
                                     <span class=" "  style="color:green; float:right" >Interest Registered</span>
                                        
                                    @else
                                        @if(!empty($job->job_apply)) 
                                                 <span class=" "  style="color:green; float:right" >Applied</span>
                                        @else
                                                @if(!empty($job->job_draft)) 
                                                     <span class=" "  style="color:red; float:right" >Draft</span>
                                                @endif

                                        @endif
                                    @endif
                                    

                                @endif
                            @endif


                            <?php /*
          
                            @if(empty($job->submit_status   ))
                                @if(isset($job->submit_status ))
                                    <span class="glyphicon glyphicon-ok"  style="color: #C55762;"></span>
                                @endif 
                            @else
                                @if($job->submit_status == 1)
                                     <span class="glyphicon glyphicon-ok"  style="color: #24b324;"></span>
                                @endif 
                            @endif */
                            ?>
         
                    </div>

                    @if(!empty($job->jobprogressstatus  ))
                        <div>Progress Status: <?php
                            $jobmatchstatus =Config::get('ins.jobappliedprogressstatus');
                            if (array_key_exists($job->jobprogressstatus, $jobmatchstatus)) {
                                 echo    $jobmatchstatus[$job->jobprogressstatus];
                            }else{
                                echo  $jobmatchstatus[0];
                            }
                            ?>
                            </div>
                     @endif 
         


                    <div>Agency: {{$job->agency_name}}</div>
                    
                    @if(!empty($job->job_apply_date))
                        <div class="col-xs-12 col-sm-12 col-md-12 description jobpadding" style="padding-top: 5px;padding-bottom: 5px">Applied on 
                         @if(!empty($job->job_apply_date)){{ Format::displayDateTime($job->job_apply_date  )  }} @else&nbsp;@endif
                        </div>
                    @endif
                </div>
                <div class="col-xs-12 col-sm-5 col-md-5 jobpadding">
                    <div class="col-xs-12 col-sm-12 col-md-12 jobpadding">
                        <div class="col-xs-4 col-sm-5 col-md-5 jobpadding">
                            Date Posted
                        </div>
                        <div class="col-xs-8 col-sm-7 col-md-7 jobpadding">
         
                           @if(!empty($job->appreoved_date)){{ Format::displayDate($job->appreoved_date  )  }} @else&nbsp;@endif

                        </div>
                        <div class="col-xs-4 col-sm-5 col-md-5 jobpadding">
                            Location
                        </div>
                        <div class="col-xs-8 col-sm-7 col-md-7 jobpadding">
                            {{$job->location}}
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-12 col-md-12 jobpadding" style="margin-bottom:10px;">
                        <div class="col-xs-4 col-sm-5 col-md-5 jobpadding">
                            Category
                        </div>
                        <div class="col-xs-8 col-sm-7 col-md-7 jobpadding">
                            {{$job->category_name}}
                        </div>
                        <div class="col-xs-4 col-sm-5 col-md-5 jobpadding">
                            Grade
                        </div>
                        <div class="col-xs-8 col-sm-7 col-md-7 jobpadding">
                            {{$job->job_grade}}
                        </div>
                        <div class="col-xs-4 col-sm-5 col-md-5 jobpadding" >
                            Salary
                        </div>
                        <div class="col-xs-8 col-sm-7 col-md-7 jobpadding">
                            ${{number_format($job->salary_from)}} - ${{number_format($job->salary_to)}}
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
   @else

    No result found
@endif
       