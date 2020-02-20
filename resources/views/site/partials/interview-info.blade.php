@extends('site.layouts.master-iframe')
@section('content')
    <section class="inter-info jobPost">
        <div class="container">
            <div class="row" style="padding-bottom: 50px; margin: 10px">
                <div class="col-xs-12 col-md-5 jobDetails">
                    <div class="row">
                        <div class="col-xs-12"><h4 class="jobName">{{$user_det->new_firstname}}  {{$user_det->new_surname}}</h4></div>
                    </div>
                    @if(!empty($user_det->new_pwpositiontitle))
                        <div class="row">
                            <div class="col-xs-4 col-sm-4 col-md-4">
                                <h6>Role Title</h6>
                            </div>
                            <div class="col-xs-8 col-sm-8 col-md-8">
                                <h6>@if(!empty($user_det->new_pwpositiontitle)){{$user_det->new_pwpositiontitle}}
                                    @else&nbsp;@endif</h6>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4">
                            <h6>Phone</h6>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <h6>@if(!empty($user_det->new_personalmobilenumber)){{$user_det->new_personalmobilenumber}}
                                @else&nbsp;@endif</h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4">
                            <h6>Email</h6>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <h6>@if(!empty($user_det->new_personalemail)){{$user_det->new_personalemail}}
                                @else&nbsp;@endif</h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4">
                            <h6>&nbsp;</h6>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">

                        </div>
                    </div>
                </div>
                <div class="hidden-sm hidden-xs col-md-2" style="margin-top:85px;">
                    <img class="img-responsive darrow" src="/site/img/arrow.jpg" alt="arrow"/>
                </div>
                <div class="hidden-md hidden-lg col-sm-12 text-center">
                    <img class="img-responsive marrow" src="/site/img/mobilearrow.jpg" alt="arrow"/>
                </div>
                <div class="col-xs-12 col-md-5 jobDetails">
                    <div class="row">
                        <div class="col-xs-12"><h4 class="jobName">{{$job_det->job_title}}</h4></div>
                    </div>
                    @if(!empty($job_det->appreoved_date))
                        <div class="row">
                            <div class="col-xs-4 col-sm-4 col-md-4">
                                <h6>Posted</h6>
                            </div>
                            <div class="col-xs-8 col-sm-8 col-md-8">
                                <h6>@if(!empty($job_det->appreoved_date)){{ \App\Models\Factories\Format::displayDate($job_det->appreoved_date  )  }}
                                    @else&nbsp;@endif</h6>
                            </div>
                        </div>
                    @endif
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4">
                            <h6>Location</h6>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <h6>@if(!empty($job_det->location)){{$job_det->location}}
                                @else&nbsp;@endif</h6>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4">
                            <h6>Agency</h6>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <h6>@if(!empty($job_det->agency_name)){{$job_det->agency_name}}
                                @else&nbsp;@endif</h6>

                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-4 col-sm-4 col-md-4">
                            <h6>Email</h6>
                        </div>
                        <div class="col-xs-8 col-sm-8 col-md-8">
                            <h6>@if(!empty($job_det->prepared_by_email)){{$job_det->prepared_by_email}}
                                @else&nbsp;@endif</h6>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script type="text/javascript">
        window.c1resize = function () {
            window.parent.postMessage(
                // get height of the content
                {
                    'module': 'iframe1',
                    'height': Math.max($('.inter-info div').outerHeight() + 10, $('.ins').outerHeight() + 60)
                }
                // set target domain
                , "*"
            )
        };
        $(function () {
            window.c1resize();
        })
    </script>
@endsection