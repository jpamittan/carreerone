@extends('site.layouts.master-iframe')

@section('content')

    <section class="inter-info">
        <div class="container-fluid insProfile mPost">
            <div class="container mPost">
                <div class="col-sm-12 col-md-12 insProfileMain mPost">
                    <div class="col-xs-12 col-sm-6 col-md-6 insProfileSmall">
                        <h4 class="inspHeader">Experience/Work</h4>
                        <div class="col-xs-12 col-sm-12 col-md-12 insinnerProfile">
                            @if(!empty($workhistory))
                                @foreach($workhistory as $history)
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="col-xs-4 col-sm-12 col-md-4">
                                            <h6>{{$history->start_date_year}}-{{$history->end_date_year}}</h6>
                                        </div>
                                        <div class="col-xs-8 col-sm-12 col-md-8">
                                            <h6 class="insaccHeader">{{$history->job_title}}</h6>
                                            <h6 class="insaccsubHeader">{{$history->company_name}}</h6>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <h4 class="inspHeader" style="color:red">No Work Experience</h4>
                                </div>

                            @endif
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6 insProfileSmall">
                        <h4 class="inspHeader">Qualifications</h4>

                        <div class="col-xs-12 col-sm-12 col-md-12 insinnerProfile">
                            @if(!empty($education))
                                @foreach($education as $edu)
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="col-xs-4 col-sm-12 col-md-4">
                                            <h6>{{$edu->start_date_year}}-{{$edu->end_date_year}}</h6>
                                        </div>
                                        <div class="col-xs-8 col-sm-12 col-md-8">
                                            <h6 class="insaccHeader">{{$edu->qualification}}</h6>
                                            <h6 class="insaccsubHeader">{{$edu->institution}}</h6>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="col-xs-12 col-sm-12 col-md-12">
                                    <h4 class="inspHeader" style="color:red">No Education History</h4>
                                </div>

                            @endif

                        </div>
                    </div>
                    {{--<div class="col-xs-12 col-sm-4 col-md-4 insProfileSmall mbottom">--}}
                        {{--<h4 class="inspHeader">Skills match</h4>--}}
                        {{--<div class="col-xs-12 col-md-12 insinnerProfile insProfileSmall">--}}
                            {{--<div class="col-xs-12 col-md-12">--}}
                                {{--<h4 class="relavantSkills">Relevent Skills</h4>--}}
                                {{--@if(!empty($skills))--}}
                                    {{--@foreach($skills as $skill)--}}
                                        {{--<div class="col-xs-9 col-sm-9 col-md-9">--}}
                                            {{--<h6 class="insRati">{{$skill->skill_name}}</h6>--}}
                                        {{--</div>--}}
                                    {{--@endforeach--}}
                                {{--@else--}}
                                    {{--<div class="col-xs-12 col-sm-12 col-md-12">--}}
                                        {{--<h4 class="inspHeader" style="color:red">No Relevant Skills</h4>--}}
                                    {{--</div>--}}

                            {{--@endif--}}
                            {{--<!-- <div class="col-xs-3 col-sm-3 col-md-3">--}}
														{{--<h6 class="insRati">5.6</h6>--}}
													{{--</div> -->--}}

                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                </div>
            </div>
        </div>
    </section>

    <script type="text/javascript">

        window.c1resize = function () {

            window.parent.postMessage(
                // get height of the content
                {
                    'module': 'iframe3',
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