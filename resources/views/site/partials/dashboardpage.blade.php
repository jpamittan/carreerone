 <?php use App\Models\Factories\Format; ?>
@if($bln)
    @include('site.partials.dashboardpagemain', array('jobs' => $jobs,'job_latest' =>$job_latest))
@else
    @foreach($jobs as $job)
        <div  class="col-xs-12 col-sm-12 col-md-12  sort sort1 jobpadding" >
            <div class="col-xs-12 col-sm-7 col-md-7 jobpadding">
               <div class="col-xs-12 col-sm-12 col-md-12 jobpadding">
                <a href ="{{URL::route('site-get-jobs', ['job_id' => $job->id])}}">{{$job->job_title}}</a>&nbsp;&nbsp;&nbsp;&nbsp;
                <?php
                    if(!empty($job->job_apply)) {
                        foreach ($job_latest as $key => $value) {
                            if($job->id == $value->job_id && $value->status == 1)
                                echo '<span class="glyphicon glyphicon-ok"  style="color: #24b324;"></span>';
                            else if($job->id == $value->job_id && $value->status == 0)
                                echo '<span class="glyphicon glyphicon-ok"  style="color: #C55762;"></span>';
                        }
                    }
                ?>
               </div>
               <div>Agency: {{$job->agency_name}}</div>
               <div class="col-xs-12 col-sm-12 col-md-12 description jobpadding" id="description">
                {{str_limit($job->position_description, $limit = 167, $end = '......')}}
                @if(!empty($job->job_apply_date))
                    <div class="col-xs-12 col-sm-12 col-md-12 description jobpadding" style="padding-top: 5px;padding-bottom: 5px">Applied on 
                        @if(!empty($job->job_apply_date)){{ Format::displayDate($job->job_apply_date  )  }}
                                                            @else&nbsp;@endif
                    </div>
                @endif
               </div>
            </div>
            <div class="col-xs-12 col-sm-5 col-md-5 jobpadding">
               <div class="col-xs-12 col-sm-12 col-md-12 jobpadding">
                    <div class="col-xs-4 col-sm-5 col-md-4 jobpadding">
                        Date Posted
                    </div>
                    <div class="col-xs-8 col-sm-7 col-md-8 jobpadding">
                       
                         @if(!empty($job->appreoved_date)){{ Format::displayDate($job->appreoved_date  )  }} @else&nbsp;@endif

                    </div>
                    <div class="col-xs-4 col-sm-5 col-md-4 jobpadding">
                        Location
                    </div>
                    <div class="col-xs-8 col-sm-7 col-md-8 jobpadding">
                        {{$job->suburb}}  {{$job->state}}
                    </div>
               </div>
               <div class="col-xs-12 col-sm-12 col-md-12 jobpadding" style="margin-bottom:10px;">
                    <div class="col-xs-4 col-sm-5 col-md-4 jobpadding" >
                        Salary
                    </div>
                    <div class="col-xs-8 col-sm-7 col-md-8 jobpadding">
                        {{$job->salary_package}}
                    </div>
               </div>
            </div>
        </div>
    @endforeach 
    <div class="col-xs-12 col-sm-12 col-md-12 jobpadding jobListPagination" style="text-align: center;">
      @include('site.layouts.pagination', ['paginator' => $jobs])
    </div>
@endif