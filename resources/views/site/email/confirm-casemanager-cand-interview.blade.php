
@extends('site.email.master-eit')

@section('content')

<tr>
    <td class="one-column">
        <table width="100%">
            <tr>
                <td class="inner contents" style="background-color:#ffffff; padding:20px; text-align: center;">
               
					<h2 style='color: #BE0071; font-weight: 800;'>Hi @if(!empty($email->first_name)){{$email->first_name}}@endif  @if(!empty($email->last_name)){{$email->last_name}}@endif,</h2>
					<p>
						<h5>Interview  is confirmed for the Candidate  {{$user_det->first_name}}  {{$user_det->last_name}} at {{$interview_det->interview_time}} for the following position - </h5>
						<h6>Role Title: {{$job_det->job_title}}</h6>
						<h6>AgencyName: {{$job_det->agency_name}}</h6>
						<h6>Grade: {{$job_det->job_grade}}</h6>
						<h6>Salary: {{$job_det->location}}</h6>
					</p>

					<h2 style='color: #BE0071; font-weight: 800;'>Recruiter Detail:</h2>


					<p>
						 
						 <?php
						 	$prepared_by_name = $job_det->prepared_by_name;
						 	$prepared_by_name_arr  = explode(",",  $job_det->prepared_by_name);;
						 	if(isset($prepared_by_name_arr) && count($prepared_by_name_arr)){
						 		if(isset($prepared_by_name_arr[1])  ){
						 			$prepared_by_name = trim($prepared_by_name_arr[1]);
						 		}
						 	}

						 ?>
						<h6>Name: {{$prepared_by_name}}</h6>
						<h6>Email: {{$job_det->prepared_by_email}}</h6>
						 
					</p>



					 @if(!empty($comments))
							<BR><BR>
							<h5>Comments :</h5>
						 	<p>{{nl2br($comments)}}</p>

						@endif

                     
                </td>
            </tr>
            
        </table>
    </td>
</tr>


@endsection

 