
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
                <td class="inner contents" style="background-color:#ffffff; padding:20px;">
					<h3>Hi {{$prepared_by_name}},</h3>
					
					<p>Unfortunately, we are yet to receive the requested information for the <b>{{$job_det->job_title}}</b> (req no. <b>{{$job_det->vacancy_reference_id}}</b>). As the deadline for matching is today, it would be greatly appreciated if the information could be provided ASAP to ensure we have time to review and notify you of any matches by COB today.</p>

					<p>Please click <a href="{{Config::get('app.url')}}site/role_description/{{$id}}">here</a> to upload the RD, advertisement and confirm the details of the role.</p> 

					<p>If the information is not provided by 11am today, advertising will need to be postponed until INS receive the necessary information and are provided reasonable time to respond.</p>

					<p>Many thanks,<br>INS Mobility Team<br>02 9119 6000</p>
                </td>
            </tr>
            
        </table>
    </td>
</tr>


@endsection

 