 <?php use App\Models\Factories\Format; ?>
@foreach($jobs as $job)

    <div  class="col-xs-12 col-sm-12 col-md-12  sort sort1 jobpadding">
        <div class="col-xs-12 col-sm-7 col-md-7 jobpadding">
           <div class="col-xs-12 col-sm-12 col-md-12 jobpadding font-title">
                <a href ="{{URL::route('site-get-jobs', ['job_id' => $job->id])}}">{{$job->job_title}}</a>
                &nbsp;&nbsp;&nbsp;&nbsp;

 
  
                    @if(empty($job->submit_status   ))
                        @if(isset($job->submit_status ))
                            <span class="glyphicon glyphicon-ok"  style="color: #C55762;"></span>
                        @endif 
                    @else
                        @if($job->submit_status == 1)
                             <span class="glyphicon glyphicon-ok"  style="color: #24b324;"></span>
                        @endif 
                    @endif 
 
            </div>
            <div>Agency: {{$job->agency_name}}</div>
            <div class="col-xs-12 col-sm-12 col-md-12 description jobpadding" id="description">
                {{str_limit($job->position_description, $limit = 167, $end = '......')}}
            </div>
            @if(!empty($job->job_apply_date))
                <div class="col-xs-12 col-sm-12 col-md-12 description jobpadding" style="padding-top: 5px;padding-bottom: 5px">Applied on 
                 @if(!empty($job->job_apply_date)){{ Format::displayDate($job->job_apply_date  )  }} @else&nbsp;@endif
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
                <div class="col-xs-4 col-sm-5 col-md-5 jobpadding" >
                    Expiry
                </div>
                <div class="col-xs-8 col-sm-7 col-md-7 jobpadding">
              
                   @if(!empty($job->deadline_date)){{ Format::displayDate($job->deadline_date  )  }} @else&nbsp;@endif

                </div>
            </div>
        </div>
    </div>
@endforeach
<div class="col-xs-12 col-sm-12 col-md-12 jobpadding jobListPagination" style="text-align: center;">
    @include('site.layouts.pagination', ['paginator' => $jobs])
</div>