<div class="col-sm-12 col-md-6 jobHeader">
    <div class="col-md-12">
        <img alt="jobmatch" src="/site/img/skillsmatch1.jpg" class="img-responsive jobImg">
        @if(!empty($score))
            <div style="margin-top: -40px;  z-index: 999;position: relative; font-size: 24px;">{{$score}}</div>
        @endif
        <h4>Capability Matching</h4>
        <div class="col-xs-12 col-sm-12 col-md-12 paddingtabright">
            @if(!empty($capabilities))
                @foreach($capabilities as $key => $value)
                    <div class="col-xs-12 col-sm-12 col-md-12 mainCapapuity tablet">
                        <div class="col-md-2">
                            <img alt="jobmatch" src="{{$value[0]['image']}}" class="img-responsive">
                            <h5 class="perHead">{{$key}}</h5>
                        </div>
                        <div class="col-xs-12 col-sm-12 col-md-10 nameMain capabulityDiv">
                            @foreach($value as $match)
                                <div class="col-xs-8 col-sm-9 col-md-9 capabulityDivName">
                                    <h6 class="joNames">
                                        {{$match['capabilities']}}
                                    </h6>
                                </div>
                                @if($match['core_status'] == 1)
                                    <div class="col-xs-3 col-sm-2 col-md-2">
                                        <h6 class="accepted1"><b>{{$match['level']}}</b></h6>
                                    </div>
                                @else
                                    <div class="col-xs-3 col-sm-2 col-md-2">
                                        <h6 class="accepted">{{$match['level']}}</h6>
                                    </div>
                                @endif
                                @if($match['score'] == 0.5)
                                    <div class="col-xs-1 col-sm-1  col-md-1">
                                        <img alt="correct" src="/site/img/correct.jpg"
                                             class="img-responsive tickCorrect img1">
                                    </div>
                                @else
                                    <div class="col-xs-1  col-sm-1 col-md-1">
                                        <span class="glyphicon glyphicon-minus minuscl"></span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @else
                <div class="col-xs-12 col-sm-12 col-md-12 mainCapapuity tablet">
                    <h5 style="color:red">Your Capability Match is Not Yet Done....</h5>
                </div>
            @endif
        </div>


    </div>
</div>