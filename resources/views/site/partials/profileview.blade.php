<div>
    <div class="col-md-12 col-sm-12 col-xs-12 wind-marg" >
        <div class="col-md-6 col-sm-6 col-xs-12 marginterview">
            <div class="col-md-5 col-sm-6  col-xs-6">
                <b>First Name</b>
            </div>
            <div class="col-md-7 col-sm-6  col-xs-6 margprof-bott">
                @if(!empty($profile_det)){{$profile_det->new_firstname}}@endif
            </div>
            <div class="col-md-5 col-sm-6  col-xs-6">
                <b>Mobile Number</b>
            </div>
            <div class="col-md-7 col-sm-6  col-xs-6 margprof-bott">
                @if(!empty($profile_det)){{$profile_det->new_personalmobilenumber}}@endif
            </div>


        </div>
        <div class="col-md-6 col-sm-6 col-xs-12 marginterview">
            <div class="col-md-5 col-sm-6  col-xs-6">
                <b>Surname</b>
            </div>
            <div class="col-md-7 col-sm-6  col-xs-6 margprof-bott">
                @if(!empty($profile_det)){{$profile_det->new_surname}}@endif
            </div>
            <div class="col-md-5 col-sm-6  col-xs-6">
                <b>Phone Number</b>
            </div>
            <div class="col-md-7 col-sm-6  col-xs-6 margprof-bott">
                @if(!empty($profile_det)){{$profile_det->new_personalhomenumber}}@endif
            </div>
        </div>
        <div class="col-md-6 col-sm-6 col-xs-12 marginterview">
        <div class="col-md-5 col-sm-6  col-xs-6">
                <b>Preferred Email</b>
            </div>
            <div class="col-md-7 col-sm-6  col-xs-6 margprof-bott">
                @if(!empty($profile_det)){{$profile_det->new_personalemail}}@endif
            </div>

            
            
        </div>
    </div>
    </div>
    <div class="col-md-12 col-sm-12 col-xs-12 sort">
    <div class="col-md-6 col-sm-6 col-xs-6 head-marg">
        <h4 class="">Identify Diversity Groups</h4>
    </div>
    </div>
    <div class="col-md-12 col-sm-12 col-xs-12  intervconfirm emergency-contact">
    <div class="col-md-12 col-sm-12 col-xs-12 wind-marg" >
        <div class="col-md-12 col-sm-12 col-xs-12 marginterview">
            <div class="col-md-6 col-sm-6  col-xs-6">
                <b>Culturally or liguistically diverse</b>
            </div>
            <div class="col-md-4 col-sm-4  col-xs-4 margprof-bott">
                <div class="col-md-12 col-sm-12 col-xs-12">
                @if(!empty($profile_det))
                    @if($profile_det->ins_culturallyandlinguisticallydiverse == 0)
                        No
                    @else
                        Yes
                    @endif
                @endif
                </div>
            </div>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12 marginterview">
            <div class="col-md-6 col-sm-6  col-xs-6">
                <b>Aboriginal or Torres Strait Islander</b>
            </div>
            <div class="col-md-4 col-sm-4  col-xs-4 margprof-bott">
                <div class="col-md-12 col-sm-12 col-xs-12">
                @if(!empty($profile_det))
                    @if($profile_det->new_atsi == 0)
                        No
                    @else
                        Yes
                    @endif
                @endif
                </div>
            </div>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12 marginterview">
            <div class="col-md-6 col-sm-6  col-xs-6">
                <b>Disability</b>
            </div>
            <div class="col-md-4 col-sm-4  col-xs-4 margprof-bott">
                <div class="col-md-12 col-sm-12 col-xs-12">
                @if(!empty($profile_det))
                    @if($profile_det->ins_disability == 0)
                        No
                    @else
                        Yes
                    @endif
                @endif
                </div>
            </div>
        </div>
        @if($profile_det->ins_disability == 1)
        <div class="col-md-12 col-sm-12 col-xs-12 marginterview">
            <div class="col-md-6 col-sm-6  col-xs-6">
                <b>Reasonable adjustment required</b>
            </div>
            <div class="col-md-6 col-sm-6  col-xs-6 margprof-bott">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    @if(!empty($profile_det) and !empty($profile_det->ins_reasonableadjustmentrequired))
                    Yes  
                    @else
                    No
                    @endif
                </div>
            </div>
        </div>   
        @if(!empty($profile_det) and $profile_det->ins_reasonableadjustmentrequired)
            <div class="col-md-12 col-sm-12 col-xs-12 marginterview">
                <div class="col-md-12 col-sm-12 col-xs-12">
                  {!! nl2br($profile_det->ins_reasonableadjustmentrequired) !!}
                 </div> 
            </div> 
        @endif
        @endif

    </div>
    </div>
    <div class="col-md-12 col-sm-12 col-xs-12 sort">
    <div class="col-md-6 col-sm-6 col-xs-6 head-marg">
        <h4 class="">Emergency Contact</h4>
    </div>
    </div>
    <div class="col-md-12 col-sm-12 col-xs-12  intervconfirm emergency-contact">
    <div class="col-md-12 col-sm-12 col-xs-12 wind-marg" >
        <div class="col-md-6 col-sm-6 col-xs-12 marginterview">
            <div class="col-md-5 col-sm-6  col-xs-6">
                <b>Contact Name</b>
            </div>
            <div class="col-md-7 col-sm-6  col-xs-6 margprof-bott">
                @if(!empty($profile_det)){{$profile_det->new_emergencycontactname or '&nbsp;'}}@endif
            </div>
            <div class="col-md-5 col-sm-6  col-xs-6">
                <b>Relationship</b>
            </div>
            <div class="col-md-7 col-sm-6  col-xs-6 margprof-bott">
                @if(!empty($profile_det)){{$profile_det->new_emergencyrelationship or '&nbsp;'}}@endif
            </div>
        </div>
        <div class="col-md-6 col-sm-6 col-xs-12 marginterview">
            <div class="col-md-5 col-sm-6  col-xs-6">
                <b>Contact Number</b>
            </div>
            <div class="col-md-7 col-sm-6  col-xs-6 margprof-bott">
                @if(!empty($profile_det)){{$profile_det->new_emergencycontactnumber or '&nbsp;'}}@endif
            </div>
            <div class="col-md-5 col-sm-6  col-xs-6">
                <b>Email</b>
            </div>
            <div class="col-md-7 col-sm-6  col-xs-6 margprof-bott">
                @if(!empty($profile_det)){{$profile_det->new_emergencyemail or '&nbsp;'}}@endif
            </div>
        </div>
    </div>
</div>