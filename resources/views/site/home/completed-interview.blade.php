@extends('site.layouts.master-dashboard')

@section('content')
					<section>
					<div class="container" style="background-color: white;">
						<div class = "col-md-12 bck-color">
							<div class= "col-xs-12 col-sm-12 col-md-12 jobview-header">
								@include('site.partials.jobtitle-sch',array('job' => $job))
								@include('site.partials.job-description-sch' ,array('job' => $job))
								
								</div>
								@include('site.partials.capabilitymatch',array('skill_match' => $skill_match))
							</div>
						</div>
				</section>
						
@endsection
