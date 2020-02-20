
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
						Application is closed for this specific role 
					</p>

					 
					<p  >
							<h5>Title: {{$details->job_title}}</h5>
							<h5>Agency Name: {{$details->agency_name}}</h5>
							<h5>Deadline Date: {{$details->deadline_date}}</h5>
					</p>

                     
                </td>
            </tr>
            
        </table>
    </td>
</tr>


@endsection
 