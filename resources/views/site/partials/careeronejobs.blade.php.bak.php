<section>
    <div class="row">
         <div class="col-xs-12 col-sm-12 col-md-12 jobmatchYout paddingNone">
            <div class="col-xs-12 col-sm-12 col-md-12 sort">
                <h3 class="col-sm-9 col-md-8 count_matches paddingNone pull-left">{{$jobCount}} jobs found</h3>
                <div class="col-sm-3 col-md-4 mostRelavant paddingNone">
                    
                    
                </div>
            </div>
            
        </div>
    </div>
</section> 
 

 @foreach($jobs as $job)

    <div class="one_search_result  " data-id="{{$job['@attributes']['ID']}}" >
        <div class="row jobmatchYout" style="padding-top:15px; padding-bottom: 15px;">

            <div class="search_results_inner">
                <div class="col-md-8 col-sm-8 col-xs-12">
                    <div class="wrapper_left">
                        <div class="job_title">
                            <a href="{{$job['URL']}}?WT.mc_n=ins" target="_blank">
                                                           {{$job['Title']}}
                                                    </a>
                        </div>
                        @if(isset($job['CompanyName']))
                        <div class="job_company">
                                        {{$job['CompanyName']}}
                                        </div>
                        @endif

                        
                        <div class="job_description">{{$job['Summary']}}</div>
                    </div>
                </div>
                <div class="col-md-4 col-sm-4 col-xs-8">
                     <div class="wrapper_middle">
                        @if($job['Days'] == 0)
                            <div class="job_new">New!</div>
                        @elseif($job['Days'] == 1)
                            <div class="job_day">Yesterday</div>
                        @else
                            <div class="job_day">{{$job['Days']}} days ago</div>
                        @endif
                        <div class="job_location">
                            {{$job['Location']['City']}} {{$job['Location']['State']}}
                        </div>
                        @if(isset($job['Salary']))
                        <div class="job_salary">{{$job['Salary']['String']}}</div>
                        @endif
                          
                        
                    </div>

 
                                <div style="clear:both"></div>
                </div>
                <div style="clear:both"></div>
            </div>
        </div>
    </div>
@endforeach