@extends('site.layouts.master')

@section('content')

  <?php /* 
<section>
 
  <div class="container-fluid ">
    <div class="container container-whitebg" style="margin-top:-50px;">
        <div class="row">
             <div class="col-xs-12 col-sm-12 col-md-12  ">
                <ul class="nav nav-tabs">

                 <li class="active"><a data-toggle="tab" href="#scheduleinterview" id="ctab" data-val="scheduleinterview">Schedule Interview</a></li>
 @if(!empty($candidates))
                  <li class=""><a data-toggle="tab" href="#screening" id="ctab" data-val="screening">Screening</a></li>
    @endif
                </ul>
            </div>
        </div>
    </div>
  </div>
</section>
 */ ?>

<div class="tab-content">
  <div id="scheduleinterview" class="tab-pane fade   active in">
         @include('site.partials.schedule-interview'  )
  </div>
  <?php /* 
@if(!empty($candidates))
  <div id="screening" class="tab-pane fade  ">
        @include('site.partials.schedule-screening'  )
  </div>  
@endif

*/ ?>
</div>


 
 
	

@endsection