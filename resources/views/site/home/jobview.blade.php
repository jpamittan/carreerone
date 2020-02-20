@extends('site.layouts.master-dashboard')

@section('content')
<section>
	<div class="container container-whitebg" style="background-color: white;">
	<div class="col-xs-12 col-sm-12 col-md-12  ">
		<div style="margin-top:-30px; padding-bottom: 20px; margin-left: 10px;"> <a href="/site/dashboard">Back</a>
	</div>

		<div class = "col-md-12 bck-color">
			<div class= "col-xs-12 col-sm-8 col-md-8 jobview-header">
				@include('site.partials.jobtitle',array('job' => $job))
				@include('site.partials.jobdescription' ,array('job' => $job,'description' => $description))
			</div>
			<div class= "col-xs-12 col-sm-4 col-md-4">
				@include('site.partials.capabilitymatch',array('skill_match' => $skill_match,'capabilities' => $capabilities))
				@include('site.partials.keywords',array('keywords' => $keywords))
			</div>
		</div>
	</div>
</section>				
@endsection
