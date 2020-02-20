
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
					
					<p>A friendly reminder to provide the requested information for the <b>{{$job_det->job_title}}</b> (req no. <b>{{$job_det->vacancy_reference_id}}</b>)</p>

					<p>Please click <a href="{{Config::get('app.url')}}site/role_description/{{$id}}">here</a> to upload the RD, advertisement and confirm the details of the role.</p> 

					<p>If you have not been notified of a suitable match by COB<b>{{date('l jS F Y', strtotime($job_det->deadline_date))}}</b>, please proceed with your recruitment pool appointment or external advertising.</p>

					<p>Many thanks,<br>INS Mobility Team<br>02 9119 6000</p>
                </td>
            </tr>
            
        </table>
    </td>
</tr>


@endsection

 