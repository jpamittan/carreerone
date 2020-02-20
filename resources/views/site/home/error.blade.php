@extends('site.layouts.master')

@section('content')
	
				<section>
					<div class = "container container-whitebg" style="text-align:center;">
						<div class = "col-md-12 bck-color">
							<div class="col-xs-12 col-sm-12 col-md-12 jobview-header" style=" ">
								<h4>{{$message}}</h4>

								<BR><BR>

								<a href="{{Config::get('app.url')}}">Back</a>
							</div>
							

						</div>
					</div>
				</section>
						
@endsection
