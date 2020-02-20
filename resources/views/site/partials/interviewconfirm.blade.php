 <?php use App\Models\Factories\Format; ?>
<section class="applySection">
  <div class="row">
    <div class="col-md-12 col-sm-12 col-xs-12 jobmatchYout paddingNone">
        <div class="col-md-12 col-sm-12  col-xs-12  sort">
          <h3 class="">Upcoming Interviews</h3>
        </div>
        @if(!empty($schedule_interview_candidate))
          @foreach($schedule_interview_candidate as $scheduled)
            <div class="col-md-12 col-sm-12  col-xs-12  intervconfirm inconfirmmarg">
                <div class="col-md-12 col-sm-12  col-xs-12 sche-paddingnone">
                  <div class="col-md-6 col-sm-6  col-xs-12 sche-paddingmarg">
                    <div class="col-md-6  col-sm-4  col-xs-6 sche-paddingnone">
                      <b>Role Title</b>
                    </div>
                    <div class="col-md-6  col-sm-8  col-xs-6 sche-paddingnone">
                      <a href="{{URL::route('scheduled-details', ['job_id' => $scheduled->id])}}">{{$scheduled->job_title}}</a>
                    </div>
                  </div> 
                  <div class="col-md-6 col-sm-6  col-xs-12 sche-paddingmarg">
                    <div class="col-md-5  col-sm-6 col-xs-6 sche-paddingnone">
                        <b>Interview Date</b>
                    </div>
                    <div class="col-md-7  col-sm-6 col-xs-6 sche-paddingnone">
                      @if(!empty($scheduled->interview_date)){{ Format::displayDate($scheduled->interview_date)}} @else&nbsp;@endif
                    </div>
                  </div> 
                </div> 
                <div class="col-md-12 col-sm-12  col-xs-12 sche-paddingnone">
                 <div class="col-md-6 col-sm-6  col-xs-12 sche-paddingmarg">
                    <div class="col-md-6  col-sm-4  col-xs-6 sche-paddingnone">
                      <b>Salary</b>
                    </div>
                    <div class="col-md-6  col-sm-8  col-xs-6 sche-paddingnone">
                      ${{number_format($scheduled->salary_from)}} - ${{number_format($scheduled->salary_to)}}
                    </div>
                 </div> 
                 <div class="col-md-6 col-sm-6  col-xs-12 sche-paddingmarg">
                   <div class="col-md-6  col-sm-4  col-xs-6 sche-paddingnone">
                      <b>Hiring Manager Name</b>
                    </div>
                    <div class="col-md-6  col-sm-8  col-xs-6 sche-paddingnone">
                      {{$scheduled->prepared_by_name}}
                    </div>
                 </div> 
                </div> 
                <div class="col-md-12 col-sm-12 col-xs-12 sche-paddingnone">
                  <div class="col-md-6 col-sm-6 col-xs-12 sche-paddingmarg">
                    <div class="col-md-6 col-sm-4 col-xs-6 sche-paddingnone">
                      <b>Agency</b>
                    </div>
                    <div class="col-md-6 col-sm-8 col-xs-6 sche-paddingnone">
                      {{$scheduled->agency_name}}
                    </div>
                  </div> 
                  <div class="col-md-6 col-sm-6 col-xs-12 sche-paddingmarg">
                    <div class="col-md-6 col-sm-6 col-xs-6 sche-paddingnone">
                      <b>Hiring Manager Email</b>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6 sche-paddingnone">
                      {{$scheduled->prepared_by_email}}
                    </div>
                  </div> 
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12 sche-paddingnone  salamarg">
                  <div class="col-md-6 col-sm-6 col-xs-12 sche-paddingmarg">
                    <div class="col-md-6 col-sm-6 col-xs-6 sche-paddingnone">
                      <b>Hiring Manager Number</b>
                    </div>
                    <div class="col-md-6 col-sm-6 col-xs-6 sche-paddingnone">
                      {{$scheduled->prepared_by_number}}
                    </div>
                  </div> 
                </div>
            </div>
          @endforeach
        @else
          <div class="col-md-12 col-sm-12  col-xs-12  intervconfirm">
            <h4 style="colorred">No Upcoming Interviews</h4>
          </div>
        @endif
    </div>
  </div>
</section>