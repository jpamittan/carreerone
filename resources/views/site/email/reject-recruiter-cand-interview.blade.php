
@extends('site.email.master')

@section('content')
<?php
	$prepared_by_name = $job_det->prepared_by_name;
	$prepared_by_name_arr  = explode(",",  $job_det->prepared_by_name);;
	if(isset($prepared_by_name_arr) && count($prepared_by_name_arr)){
		if(isset($prepared_by_name_arr[1])  ){
			$prepared_by_name = trim($prepared_by_name_arr[1]);
		}
	}

?>
<tr>
    <td class="one-column">
        <table width="100%">
            <tr>
                <td class="inner contents" style="background-color:#ffffff; padding:20px; text-align: center;">
               
					<h2 style='color: #BE0071; font-weight: 800;'>Hi {{$prepared_by_name}},</h2>
					<p>
						<h5>Interview  has been rejected by  the Candidate  {{$user_det->first_name}}  {{$user_det->last_name}} for the following position - </h5>
						<h6>Role Title: {{$job_det->job_title}}</h6>
						<h6>AgencyName: {{$job_det->agency_name}}</h6>
						<h6>Grade: {{$job_det->job_grade}}</h6>
						<h6>Salary: {{$job_det->location}}</h6>
					</p>


					<h5> Comments : </h5> {{$comment}}
					 

                     
                </td>
            </tr>
            
        </table>
    </td>
</tr>


@endsection

 