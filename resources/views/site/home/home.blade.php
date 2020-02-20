@extends('site.layouts.master')

@section('content')

<section>
<div class="container-fluid second_section">
	<div class="container">
		<BR><BR>
		
		<a href="{{URL::to('/candidate')}}">Candidate</a>
		<BR><BR>
		<a href="{{URL::to('/candidatematch')}}">Candidate Match</a>
		<BR><BR>
		<a href="{{URL::to('/calandar')}}">Calandar</a>
		<BR><BR>
		<a href="{{URL::to('/jobview')}}">Job View</a>
		<BR><BR>
		<a href="{{URL::to('/apply')}}">Apply</a>
		<BR><BR>
		<a href="{{URL::to('/dashboard')}}">Dashboard</a>
		<BR><BR>
		
</div>
</div></section>
 
@endsection
