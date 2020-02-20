
@extends('site.email.master-eit')

@section('content')

<tr>
    <td class="one-column">
        <table width="100%">
            <tr>
                <td class="inner contents" style="background-color:#ffffff; padding:20px; text-align: center;">
               
					<h2 style='color: #BE0071; font-weight: 800;'>Hi @if(!empty($email->first_name)){{$email->first_name}}@endif  @if(!empty($email->last_name)){{$email->last_name}}@endif,</h2>
					<p>
						You have been selected for an interview for the following position -
					</p>

					 
					<p  >
						<h6>Role Title: {{$job_det->job_title}}</h6>
						<h6>AgencyName: {{$job_det->agency_name}}</h6>
						<h6>Grade: {{$job_det->job_grade}}</h6>
						<h6>Salary: {{$job_det->location}}</h6>

						

						@if(!empty($comments))
							<BR><BR>
							<h5>Address and Instructions :</h5>
						 	<p>{{nl2br($comments)}}</p>

						@endif

						@if(!empty($comments_panelmember))
							<BR><BR>
							<h5>Convenor/Panel Member :</h5>
						 	<p>{{nl2br($comments_panelmember)}}</p>

						@endif


						

						 
						<BR><BR>	

						<h5>Please click on the following link to select your preferred interview time</h5>
						<h4>{{Config::get('app.url')}}site/interview</h4>


					</p>

                     
                </td>
            </tr>
            
        </table>
    </td>
</tr>


@endsection
 