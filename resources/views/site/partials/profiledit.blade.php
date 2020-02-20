<?php $warningMessage = "Please complete all fields in this section."; ?>
<section class="profileview" <?=($user->profile_completion_email == 0) ? 'style="display:none"' : '' ?>>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12  jobmatchYout paddingNone">
            <div class="col-md-12 col-sm-12 col-xs-12 sort">
                <div class="col-md-6 col-sm-6 col-xs-6 head-marg">
                    <h3 class="">Personal Details</h3>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-6 butt-marg">
                    <span class="pull-right"><a class="btn btn-primary pers-edit" id="editprof" href="#" role="button">Edit</a></span>
                </div>
                <div class="col-sm-12 col-xs-12 col-md-12 col-lg-12">
                    <div class="alert alert-warning"><?=$warningMessage?></div>
                </div>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12  intervconfirm" id="profile-view">
                @include('site.partials.profileview')
            </div>
        </div>
    </div>
</section>
<section class="profilesubmit" <?=($user->profile_completion_email != 0) ? 'style="display:none"' : ''?>>
    <div class="row">
        <div class="col-md-12 col-sm-12 col-xs-12  jobmatchYout paddingNone">
            <div class="col-md-12 col-sm-12 col-xs-12 sort">
                <h3 class="">Edit Personal Details</h3>
                <div class="alert alert-warning"><?=$warningMessage?></div>
            </div>
            <div class="col-sm-12 padding-top-10" id="validationErrorContainer">
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12  intervconfirm">
                <form id="profieditform" method="post" action="{{URL::route('site-profile-edit')}}">
                    <input type="hidden" name="_token" value="{{{csrf_token() }}}">
                    <div class="col-md-12 col-sm-12 col-xs-12 wind-marg">
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
                                <input type="text" name="mobilenumber"
                                       value="@if(!empty($profile_det)){{$profile_det->new_personalmobilenumber}}@endif">
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
                                <input type="text" name="phonumber"
                                       value="@if(!empty($profile_det)){{$profile_det->new_personalhomenumber}}@endif">
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-6 col-xs-12 marginterview">
                            <div class="col-md-5 col-sm-6  col-xs-6">
                                <b>Preferred Email</b>
                            </div>
                            <div class="col-md-7 col-sm-6  col-xs-6 margprof-bott">
                                <input type="hidden" id="old_personalemail" name="old_personalemail"
                                       value="@if(!empty($profile_det)){{$profile_det->new_personalemail}}@endif">
                                <input style="width:200%;" type="text" name="personalemail" id="personalemail"
                                       value="@if(!empty($profile_det)){{$profile_det->new_personalemail}}@endif">

                            </div>
                        </div>
                        <div class="col-md-12 col-sm-12 col-xs-12 sort">
                            <div class="col-md-6 col-sm-6 col-xs-6 head-marg">
                                <h4 class="">Identify Diversity Group</h4>
                            </div>
                        </div>
                        <div class="col-md-12 col-sm-12 col-xs-12 marginterview intervconfirm"
                             style="padding-bottom: 20px;">
                            <div class="col-md-6 col-sm-6  col-xs-6">
                                <b>Culturally or liguistically diverse</b>
                            </div>
                            <div class="col-md-6 col-sm-6  col-xs-6">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <select name="ins_culturallyandlinguisticallydiverse">
                                        <option value=""
                                                @if($profile_det && $profile_det->new_emergencystate == "") selected @endif></option>
                                        <option value="1"
                                                @if($profile_det && $profile_det->ins_culturallyandlinguisticallydiverse == "1") selected @endif>
                                            Yes
                                        </option>
                                        <option value="0"
                                                @if($profile_det && $profile_det->ins_culturallyandlinguisticallydiverse == "0") selected @endif>
                                            No
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-sm-12 col-xs-12 marginterview" style="padding-bottom: 20px;">
                            <div class="col-md-6 col-sm-6  col-xs-6">
                                <b>Aboriginal or Torres Strait Islander</b>
                            </div>
                            <div class="col-md-6 col-sm-6  col-xs-6">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <select name="new_atsi">
                                        <option value=""
                                                @if($profile_det && $profile_det->new_atsi == "") selected @endif></option>
                                        <option value="1"
                                                @if($profile_det && $profile_det->new_atsi == "1") selected @endif>Yes
                                        </option>
                                        <option value="0"
                                                @if($profile_det && $profile_det->new_atsi == "0") selected @endif>No
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 col-sm-12 col-xs-12 marginterview" style="padding-bottom: 20px;">
                            <div class="col-md-6 col-sm-6  col-xs-6">
                                <b>Disability</b>
                            </div>
                            <div class="col-md-6 col-sm-6  col-xs-6">
                                <div class="col-md-12 col-sm-12 col-xs-12">
                                    <select name="ins_disability" id="ins_disability">
                                        <option value=""
                                                @if($profile_det && $profile_det->ins_disability == "") selected @endif></option>
                                        <option value="1"
                                                @if($profile_det && $profile_det->ins_disability == "1") selected @endif>
                                            Yes
                                        </option>
                                        <option value="0"
                                                @if($profile_det && $profile_det->ins_disability == "0") selected @endif>
                                            No
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        @if($profile_det and $profile_det->ins_disability == "1")
                            <div class="col-md-12 col-sm-12 col-xs-12 marginterview reasonable-adjustment"
                                 style="padding-bottom: 20px;">
                                @else
                                    <div class="col-md-12 col-sm-12 col-xs-12 marginterview reasonable-adjustment"
                                         style="padding-bottom: 20px; display: none;">
                                        @endif
                                        <div class="col-md-6 col-sm-6  col-xs-6">
                                            <b>Do you require reasonable adjustment in the workplace?</b>
                                        </div>
                                        <div class="col-md-6 col-sm-6  col-xs-6">
                                            <div class="col-md-12 col-sm-12 col-xs-12">
                                                <select name="ins_disability_adjustment" id="ins_disability_adjustment">
                                                    <option value=""></option>
                                                    <option value="1" <?=($profile_det and $profile_det->ins_disability == "1" and !empty($profile_det->ins_reasonableadjustmentrequired)) ? "selected" : ''?>>
                                                        Yes
                                                    </option>
                                                    <option value="0" <?=($profile_det and $profile_det->ins_disability == "1" and empty($profile_det->ins_reasonableadjustmentrequired)) ? "selected" : ''?>>
                                                        No
                                                    </option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    @if($profile_det and $profile_det->ins_disability == "1" and $profile_det->ins_reasonableadjustmentrequired != '')
                                        <div class="col-md-12 col-sm-12 col-xs-12 marginterview reasonable-adjustment-info"
                                             style="padding-bottom: 20px;">
                                            @else
                                                <div class="col-md-12 col-sm-12 col-xs-12 marginterview reasonable-adjustment-info"
                                                     style="padding-bottom: 20px; display: none;">
                                                    @endif
                                                    <div class="col-md-12 col-sm-12  col-xs-12">
                                                        <textarea style="width: 100%; height: 75px;" maxlength="2000"
                                                                  name="ins_reasonableadjustmentrequired"
                                                                  placeholder="Please specify your needs">{{$profile_det->ins_reasonableadjustmentrequired}}</textarea>
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-sm-12 col-xs-12 sort">
                                                    <div class="col-md-6 col-sm-6 col-xs-6 head-marg">
                                                        <h4 class="">Emergency Contact</h4>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-12 marginterview intervconfirm ">
                                                    <div class="col-md-5 col-sm-6  col-xs-6">
                                                        <b>Contact Name</b>
                                                    </div>
                                                    <div class="col-md-7 col-sm-6  col-xs-6 margprof-bott">
                                                        <input type="text" name="new_emergencycontactname"
                                                               value="@if(!empty($profile_det)){{$profile_det->new_emergencycontactname}}@endif">
                                                    </div>
                                                    <div class="col-md-5 col-sm-6  col-xs-6">
                                                        <b>Relationship</b>
                                                    </div>
                                                    <div class="col-md-7 col-sm-6  col-xs-6 margprof-bott">
                                                        <input type="text" name="new_emergencyrelationship"
                                                               value="@if(!empty($profile_det)){{$profile_det->new_emergencyrelationship}}@endif">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-xs-12 marginterview intervconfirm">
                                                    <div class="col-md-5 col-sm-6  col-xs-6">
                                                        <b>Contact Number</b>
                                                    </div>
                                                    <div class="col-md-7 col-sm-6  col-xs-6 margprof-bott">
                                                        <input type="text" name="new_emergencycontactnumber"
                                                               value="@if(!empty($profile_det)){{$profile_det->new_emergencycontactnumber}}@endif">
                                                    </div>
                                                    <div class="col-md-5 col-sm-6  col-xs-6">
                                                        <b>Email</b>
                                                    </div>
                                                    <div class="col-md-7 col-sm-6  col-xs-6 margprof-bott">
                                                        <input type="text" name="new_emergencyemail"
                                                               value="@if(!empty($profile_det)){{$profile_det->new_emergencyemail}}@endif">
                                                    </div>
                                                </div>
                                                <div class="col-md-12 col-sm-12  col-xs-12 pull-right"
                                                     style="margin-bottom: 10px;">
                                                    <input type="submit" class="btn btn-primary" value="Submit"
                                                           id="submit-profile-edit">
                                                    <a class="btn btn-primary pers-edit" id="profilesubmitcancel"
                                                       href="#" role="button" style="margin-top:5px;">Cancel</a>
                                                </div>
                                        </div>
                </form>
            </div>
        </div>
    </div>
</section>