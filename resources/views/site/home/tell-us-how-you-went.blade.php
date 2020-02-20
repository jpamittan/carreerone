@extends('site.layouts.master')

@section('content')
	
	<section>
		<div class = "container container-whitebg" style="text-align: center;">
			<div class = "col-md-12 bck-color clearfix">
				@if(Session::has('message'))
					<br/>
					<div class="alert {{ Session::get('alertType') }}" role='alert'>
						<label class="control-label">{{ Session::get('message') }}</label>
					</div>
				@else
					@if($submitted)
					<br/>
						<div class="alert alert-success" role='alert'>
							<label class="control-label">You have already submitted feedback</label>
						</div>
						<BR><BR>

								<a href="{{Config::get('app.url')}}">Back</a>
					@else

					<form name='frmfeedback' value='' method="post" action="{{ route('send-feedback') }}">
						<div class="row-fluid">
							<h3>Candidate Feedback</h3>	
							<hr/>
							<div class="form-group">
								<label class="control-label">Tell us how you went</label>
								<textarea class="form-control" name='feedback' value='' rows='5'></textarea>
							</div>
							<div class="form-group text-right">
								<button type="submit" class="btn btn-primary btn-md">Submit</button>
								<input type="hidden" name="_token" value="{{ csrf_token() }}">
								<input type="hidden" name="crm_id" value="{{ $crm_user_id }}">
								<input type="hidden" name="id" value="{{ str_replace('=','',base64_encode($id)) }}">
							</div>
						</div>
					</form>
					@endif	
				@endif	
			</div>
		</div>
	</section>
						
@endsection