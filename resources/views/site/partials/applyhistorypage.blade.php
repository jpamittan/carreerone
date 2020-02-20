@foreach($apply_history as $applyhistory)
<div class="col-md-12 col-sm-12 col-xs-12 applyhis-marg">
    <div class="col-md-12 col-sm-12 applyhistoryTable">
        <div class="col-md-6 col-sm-6 paddingRight">
            <h5 style="color: #5e0058;"><a href ="{{URL::route('site-get-jobs', ['job_id' => $applyhistory->id])}}">{{$applyhistory->job_title}}</a></h5>
        </div> 
        <div class="col-md-2 col-sm-2  paddingRight"> </div> 
        <div class="col-md-2 col-sm-2  paddingRight"> </div>
        <div class="col-md-2 col-sm-2  paddingRight"> </div>
    </div>
    <div class="col-sm-12 col-md-12 applyhistoryTable">
        <div class="col-md-2 col-sm-2 paddingRight cat1">
            Location
        </div> 
        <div class="col-md-3 col-sm-3 paddingRight cat1">{{$applyhistory->location}}</div> 
           @if(empty($applyhistory->submit_status))
                        @if(isset($applyhistory->submit_status) )
                             <div class="col-md-2 col-sm-2 paddingRight cat1">
                                      Saved As Draft
                                    </div>
                        @endif 
                    @else
                        @if($applyhistory->submit_status == 1)
                              <div class="col-md-2 col-sm-2 paddingRight cat1">
                              Submitted
                            </div>
                        @endif 
                    @endif 
        <div class="col-md-5 col-sm-5 paddingRight">Applied on {{date("jS M  Y", strtotime($applyhistory->created_at))}} </div>
    </div>
    <div class="col-md-12 col-sm-12 col-md-12 applyhistoryTable ">
        <div class="col-md-2 col-sm-2  paddingRight cat1">
            Category
        </div> 
        <div class="col-md-3 col-sm-3 paddingRight cat1">{{$applyhistory->category_name}} </div>
        <div class="col-md-2 col-sm-2 paddingRight"></div> 
        <div class="col-md-3 col-sm-3 col-xs-8 paddingRight"><a href="{{URL::to('/site/download_resume/'.$applyhistory->cv_id )}}" id="download">{{$applyhistory->resume_name}}</a> </div>
        <!-- <div class="col-md-1 col-sm-1 paddingRight delete delete-marg"> <a href="#">Remove</a></div> -->
        <form id="deletejob">
            <input type="hidden" name="_token" value="{{{csrf_token() }}}">
            <input type="hidden" name="job_id" value="{{$applyhistory->id}}">
        </form>
    </div>
    <div class="col-md-12 col-sm-12 col-md-12 applyhistoryTable ">
        <div class="col-md-2 col-sm-2  paddingRight cat1">
            Agency
        </div> 
        <div class="col-md-3 col-sm-3 paddingRight cat1">{{$applyhistory->agency_name}} </div>
        <div class="col-md-2 col-sm-2 paddingRight"></div> 
        <div class="col-md-3 col-sm-3 col-xs-8 paddingRight"></div>
        <div class="col-md-1 col-sm-1 paddingRight"></div>
    </div>
    <div class="col-md-12 col-sm-12 col-md-12 applyhistoryTable ">
        <div class="col-md-2 col-sm-2  paddingRight cat1">
            Grade
        </div> 
        <div class="col-md-3 col-sm-3 paddingRight cat1">{{$applyhistory->job_grade}} </div>
        <div class="col-md-2 col-sm-2 paddingRight"></div> 
        <div class="col-md-3 col-sm-3 col-xs-8 paddingRight"></div>
        <div class="col-md-1 col-sm-1 paddingRight"></div>
    </div>
    <div class="col-md-12 col-sm-12 col-md-12 applyhistoryTable " style="margin-bottom: 10px;">
        <div class="col-md-2 col-sm-2  paddingRight cat1">
            Salary
        </div> 
        <div class="col-md-3 col-sm-3 paddingRight cat1">$75,558 - $83,115 </div> 
        <div class="col-md-2 col-sm-2 paddingRight cat1"> </div> 
        <div class="col-md-3 col-sm-3 paddingRight">
            @if(Carbon\Carbon::now()->lte(Carbon\Carbon::parse($applyhistory->deadline_date)))
                {{ Carbon\Carbon::now()->diffInDays(Carbon\Carbon::parse($applyhistory->deadline_date)) }} day(s) Left
            @else
                0 day Left
            @endif
        </div>
    </div>
</div>
@endforeach
<div class="col-xs-12 col-sm-12 col-md-12 jobpadding applyhistoryPagination" style="text-align: center;">
    @include('site.layouts.pagination', ['paginator' => $apply_history])
</div>
