
@extends('site.email.master-eit')

@section('content')

<tr>
    <td class="one-column">
        <table width="100%">
            <tr>
                <td class="inner contents" style="background-color:#ffffff; padding:20px; text-align: center;">
               
					<h2 style='color: #BE0071; font-weight: 800;'>Hi {{$user_det->first_name}}  {{$user_det->last_name}},</h2>
					<p>
						<h5>Your  interview  is confirmed on {{$interview_det->interview_date}} at {{$interview_det->interview_time}} for the following position - </h5>
						<h6>Role Title: {{$job_det->job_title}}</h6>
						<h6>AgencyName: {{$job_det->agency_name}}</h6>
						<h6>Grade: {{$job_det->job_grade}}</h6>
						<h6>Salary: {{$job_det->location}}</h6>
					</p>
						@if(!empty($comments))
							<BR><BR>
							<h5>Comments :</h5>
						 	<p>{{nl2br($comments)}}</p>

						@endif
						@if(!empty($comments_panelmember))
							<BR><BR>
							<h5>Convenor/Panel Member :</h5>
						 	<p>{{nl2br($comments_panelmember)}}</p>

						@endif

					 

                     
                </td>
            </tr>
            
        </table>
    </td>
</tr>


@endsection

 