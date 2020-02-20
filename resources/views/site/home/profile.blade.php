@extends('site.layouts.master-dashboard')
@section('content')
<div class="container container-whitebg1">
    <div class="row">
        <div class="col-md-9 dbmaindas">
            @if(!empty(\Session::get('error')))
            <div class="alert alert-danger">
                {{\Session::get('error')}}
            </div>
            @endif
            @if(!empty(\Session::get('message')))
            <div class="alert alert-success">
                {{\Session::get('message')}}
            </div>
            @endif
            @include('site.partials.profiledit', array('profile_det'=>$profile_det))
            @include('site.partials.resumeupload',array('user_resume'=>$user_resume))
            @include('site.partials.location',array('profile_det'=>$profile_det,'location'=>$location,'user_location'=>$user_location))
            @include('site.partials.jobcategory',array('profile_det'=>$profile_det, 'category'=>$category, 'user_category'=>$user_category))
            {{--<iframe frameborder="0" width="100%" src="{{ route('site-get-user-capabilities', ['user_id' => $user_id]) }}"></iframe>--}}
            @include('site.partials.user-capabilities',array('user_capabilities'=>$user_capabilities))
            @if(count($skill_gap))
                @include('site.partials.skill-gap', array('skill_gap' => $skill_gap))
            @endif
            @include('site.partials.skillassesment',array('skills'=>$skills))
        </div>
        @include('site.partials.dahboardside',array('candidate_interview' => $candidate_interview, 'rss_feeds' => $rss_feeds))
    </div>
</div>
</body>
</html>
@endsection