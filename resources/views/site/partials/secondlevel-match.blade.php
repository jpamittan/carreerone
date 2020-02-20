<div class="col-sm-12 col-md-6 jobHeader">
    <div class="col-md-12">
        <img alt="jobmatch" src="/site/img/skillsmatch1.jpg" class="img-responsive jobImg">
        <h4>Skills Match</h4>
        @if(isset($skills) && !empty($skills))
            @foreach($skills as $skill)
                <div class="col-xs-12 col-sm-12 col-md-12 nameMain">
                    <div class="col-xs-10 col-md-10">
                        <h6 class="joNames">{{$skill->skill_name}}</h6>
                    </div>
                    @if($skill->status == 1)
                        <div class="col-xs-2 col-md-2">
                            <img alt="correct" src="/site/img/correct.jpg" class="img-responsive tickCorrect"
                                 style="width: 43%;">
                        </div>
                    @else
                        <div class="col-xs-1  col-sm-1 col-md-1">
                            <span class="glyphicon glyphicon-minus minusc2"></span>
                        </div>
                    @endif
                </div>
            @endforeach
        @else
            <div class="col-xs-12 col-md-12" style="margin-bottom: 20px;">
                <h5 class="joNames" style="color: #f15151;">
                    <b>No Skill Match Done For This Job</b>
                </h5>
            </div>
        @endif
    </div>
</div>



			