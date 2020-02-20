
@extends('site.email.master')

@section('content')

<tr>
    <td class="one-column">
        <table width="100%">
            <tr>
                <td class="inner contents" style="background-color:#ffffff; padding:20px;">
					<p>Please see attached the RD for <b>{{$details->job_title}}</b> (req no. <b>{{$details->vacancy_reference_id}}</b>).</p>

					<p>The following details have been confirmed:</p>

					<p><b>Workplace location: </b>{{$details->location}}</p>

					<p><b>Hiring Manager contact details:</b><br>
					Name: {{$details->prepared_by_name}}<br>
					Phone: {{$details->prepared_by_number}}<br>
					Email: {{$details->prepared_by_email}}</p>

					<p>If you have any additional questions regarding the role, please contact the Hiring Manager directly.</p>

					@if($details->employee_status_id != '100000002')
					<p><b>Length of fixed term:</b> 6 months</p>
					@endif

					<p><b>Advertisement:</b>
					@if(!empty($advert ))
						<br>{!! html_entity_decode( nl2br(e($advert)) )  !!}
					@else
						Not provided
					@endif

					<p>If you wish to be matched to the Role, please let me know ASAP. If you do not confirm by 2pm {{date('l jS F Y', strtotime($details->deadline_date))}} at the latest the match will not be progress and you will be withdrawn.</p>

					<p>Thanks,<br>[[[CASE MANAGER]]]</p>
                </td>
            </tr>
            
        </table>
    </td>
</tr>


@endsection