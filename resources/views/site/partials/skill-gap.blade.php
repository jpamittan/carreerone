<section class="applySection">
    <div class="row">
        <div>
            <div class="col-md-12 col-sm-12 col-xs-12 sort">
                <div class="col-md-6 col-sm-6 col-xs-12"> 
                    <h3 class="">Top 20 Skill Gap</h3>
                </div>
            </div>
            <div>
                <div class="col-md-6 col-sm-6 col-xs-12"> 
                    <div class="col-md-8 col-sm-8 col-xs-8"> 
                        <h6><strong>Skill Name</strong></h6>
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-4"> 
                        <h6><strong>Frequency</strong></h6>
                    </div>
                    @foreach ($skill_gap[0] as $gap)
                    <div class="col-md-8 col-sm-8 col-xs-8"> 
                        {{$gap->skill_name}}
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-4"> 
                        {{$gap->gap}}
                    </div>
                    @endforeach
                </div>
                @if(isset($skill_gap[1]))
                <div class="col-md-6 col-sm-6 col-xs-12"> 
                    <div class="col-md-8 col-sm-8 col-xs-8"> 
                        <h6><strong>Skill Name</strong></h6>
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-4"> 
                        <h6><strong>Frequency</strong></h6>
                    </div>
                    @foreach ($skill_gap[1] as $gap)
                    <div class="col-md-8 col-sm-8 col-xs-8"> 
                        {{$gap->skill_name}}
                    </div>
                    <div class="col-md-4 col-sm-4 col-xs-4"> 
                        {{$gap->gap}}
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>
    </div>
</section>