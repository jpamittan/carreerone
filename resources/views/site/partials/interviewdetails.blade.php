<section class="applySection">
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12  jobmatchYout paddingNone">
            <div class="col-md-12 col-sm-12 col-xs-12 sort">
                <h3 class="">Pending Interview Confirmation</h3>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12  intervconfirm">
              @foreach($pending_interview_candidate as $pending)
              <div class="col-md-12 col-sm-12 col-xs-12 sche-paddingnone" style="margin-bottom: 10px; ">
                    <div class="col-md-6 col-sm-6 col-xs-12 marginterview int-paddingmarg">
                        <div class="col-md-3 col-sm-6  col-xs-6 sche-paddingnone">
                            <b>Role Title:</b>
                        </div>
                        <div class="col-md-6 col-sm-6  col-xs-6 sche-paddingnone">
                            {{$pending->job_title}}
                        </div>
                    </div> 
                    <div class="col-md-3 col-sm-6  col-xs-6 cnf-rej">
                    <input type="hidden" name="pending_id" id="pending_id" value="{{$pending->id}}">
                    <a class="btn btn-primary" href="{{URL::to('/site/pending_confirmation'.'/'.$pending->id)}}" role="button">View to Confirm/Reject</a>
                    </div> 
               </div> 
             @endforeach
            </div>
        </div>
    </div>
</section>