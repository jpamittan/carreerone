@if(!empty($user_capabilities) && !empty($user_capabilities[0]['capabilities']))
<section class="applySection">
    <div class="row res-marg">
    <div class="col-md-12 col-sm-12 col-xs-12  jobmatchYout paddingNone" >
        <div class="col-md-12 col-sm-12 col-xs-12 sort">
            <div class="col-md-6 col-sm-6 col-xs-6 head-marg">
                <h3 class="">Capabilities</h3>
            </div>
        </div>
        <div class="col-md-12 col-sm-12 col-xs-12 mobile-no-padding">
            <div class="col-sm-12 col-md-12 jobHeader jobtablet">
                <div class="col-sm-12">
                    <div class="col-md-12">
                        <div class="col-xs-12 col-sm-12 col-md-12 paddingtabright">
                            <div class="col-xs-12 col-sm-12 col-md-12 mainCapapuity hidden-sm" >
                                <div class="col-md-2">
                                    <h6 class="joNames" style="font-weight: bold;">    Capability Group  </h6>
                                </div>
                                <div class="col-xs-12 col-sm-12 col-md-10 nameMain capabulityDiv">
                                    <div class="col-xs-6 col-sm-6 col-md-6 pabulityDivName">
                                        <h6 class="joNames" style="font-weight: bold;">    Capability Name  </h6>
                                    </div>
                                    <div class="col-xs-2 col-sm-2 col-md-2">
                                        <h6 class="joNames" style="font-weight: bold;">   Level </h6>
                                    </div>
                                    <div class="col-xs-2 col-sm-2  col-md-2">
                                        <h6 class="joNames" style="font-weight: bold;">INS Assessment Outcome</h6>
                                    </div>
                                    <div class="col-xs-2 col-sm-2 col-md-2">
                                        <div href="#" data-toggle="tooltip" data-placement="auto right" class="cap-tooltip" title="Percentage of potential matches where the capability was not met. Click on the percentage to see the breakdown of required capability levels.">?</div>
                                        <h6 class="joNames" style="font-weight: bold;">Gap Analysis </h6>
                                    </div>
                                </div>
                            </div>
                            @foreach($user_capabilities as $uc)
                            <div class="col-xs-12 col-sm-12 col-md-12 mainCapapuity">
                                <div class="col-md-2">
                                    <img alt="jobmatch" src="{{$uc['group']['image']}}" class="img-responsive">
                                </div>
                                @if(!empty($uc['capabilities']))
                                @foreach($uc['capabilities'] as $cap)
                                <div class="col-xs-2 col-sm-2 col-md-2"></div>
                                <div class="col-xs-12 col-sm-12 col-md-10 capabulityDiv">
                                    <div class="col-xs-6 col-sm-6 col-md-6 capabulityDivName">
                                        <h6 class="joNames"> 
                                            @if($cap->core == 1)
                                            <b>{{$cap->name}} </b>
                                            @else
                                            {{$cap->name}} 
                                            @endif
                                        </h6>
                                    </div>
                                    <div class="col-xs-2 col-sm-2 col-md-2">
                                        <h6 class="acceptedbold"> 
                                            @if($cap->core == 1)
                                            <b>{{$cap->capability}} </b>
                                            @else
                                            {{$cap->capability}} 
                                            @endif
                                        </h6>
                                    </div>
                                    <div class="col-xs-3 col-sm-2  col-md-2">
                                        <h6 class="acceptedbold"> 
                                        @if (! empty($cap->criteria))
                                            {{$cap->criteria}}
                                        @else
                                            Not Assessed
                                        @endif
                                        </h6>
                                    </div>
                                    <div class="col-xs-1 col-sm-2 col-md-2">
                                        <h6 class="acceptedbold">
                                        @if(isset($cap->mismatch) and count($cap->mismatch))
                                            @if(!empty($cap->criteria) && (strtolower($cap->criteria) == 'not met' || strtolower($cap->criteria) == 'partially met' || $cap->criteria == '-'))
                                                -
                                            @else
                                                <a href="#" class="toggle-mismatch" data-id="{{$cap->capability_name_id}}">{{ round(count($cap->mismatch) / (count($cap->match) + count($cap->mismatch)) * 100)}}%</a>
                                            @endif
                                        @else
                                            -
                                        @endif
                                        </h6>
                                    </div>
                                </div>
                                @if(isset($cap->mismatch) and count($cap->mismatch))
                                <div class="col-xs-2 col-sm-2 col-md-2"></div>
                                <div class="col-xs-10 col-sm-10 col-md-10 mismatch-outer" id="mismatch-{{$cap->capability_name_id}}">
                                    <div class="col-xs-6 col-sm-6 col-md-6">

                                    </div>
                                    <div class="col-xs-6 col-sm-6 col-md-6 mismatch-container">
                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                            <div class="col-sm-8 col-md-8">
                                                <strong>Level Not Met</strong>
                                            </div>
                                            <div class="col-sm-4 col-md-4">
                                                <strong># of Occurrences</strong>
                                            </div>
                                        </div>
                                        @foreach($cap->mismatchGroups as $mismatch)
                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                            <div class="col-sm-8 col-md-8">
                                                {{$mismatch['name']}}
                                            </div>
                                            <div class="col-sm-4 col-md-4">
                                                {{$mismatch['count']}}
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                                @endforeach
                                @endif
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endif
{{--
<div class="col-sm-6 col-md-4 jobHeader jobtablet">
    <div class="col-sm-12">
        <div class="col-md-12">
            <img alt="jobmatch" src="/site/img/skillsmatch1.jpg" class="img-responsive jobImg">
            <span>{{$score}}</span>
            <h4>Capability Matching</h4>
            <div class="col-xs-12 col-sm-12 col-md-12 paddingtabright">
                @if(!empty($capabilities))
                @foreach($capabilities as $key => $value)
                <div class="col-xs-12 col-sm-12 col-md-12 mainCapapuity">
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
                            <img alt="correct" src="/site/img/correct.jpg" class="img-responsive tickCorrect img1">
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
                <div class="col-xs-12 col-sm-12 col-md-12 mainCapapuity">
                    <h5 style="color:red">Your Capability Match is Not Yet Done....</h5>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
<section class="applySection">
    <div class="row res-marg">
        <div class="col-md-12 col-sm-12 col-xs-12  jobmatchYout paddingNone" >
            <div class="col-md-12 col-sm-12 col-xs-12 sort">
                <div class="col-md-6 col-sm-6 col-xs-6 head-marg">
                    <h3 class="">Capabilities</h3>
                </div>
                <div class="col-md-6 col-sm-6 col-xs-6 butt-marg">
                    <form id="uploadfile" action="{{URL::route('site-user-capabilities')}}" method="post"  enctype='multipart/form-data'>
                        <input type="hidden" name="_token" value="{{{csrf_token() }}}">
                        <input type="file" class="lo-prof" id="inputfile" name="inputfile">
                        <span class="pull-right"><button type="submit" class="btn btn-default  uplo-edit">Upload</button></span>
                    </form>
                </div>
            </div>
            <div class="col-md-12 col-sm-12 col-xs-12  intervconfirm">
                <div class="col-md-12 col-sm-12 col-xs-12 wind-marg" >
                    <div class="col-md-6 col-sm-6 col-xs-12 marginterview">
                        @if(!empty($user_capabs))
                        <div class="col-md-10  col-sm-8  col-xs-8 margprof-bott">
                            {{$user_capabs->name}}
                        </div>
                        <div class="col-md-1 col-sm-1  col-xs-1 margprof-bott">
                            <a href="{{URL::to('/site/download_pdf/'.$user_capabs->id )}}" id="download"> View</a>
                        </div>
                        @else
                        <h5 style="color:red;">No Resume Uploaded</h5>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
--}}