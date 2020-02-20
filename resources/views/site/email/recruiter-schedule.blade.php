
@extends('site.email.master')

@section('content')

<tr>
    <td class="one-column">
        <table width="100%">
            <tr>
                <td class="inner contents" style="background-color:#ffffff; padding:20px;">
					<p>Hi {{$details[0]->prepared_by_name}},</p>

					<p>Please see attached a resume and capability assessment report for the following candidates:</p>

					<ul>
						@foreach ($details as $det)
						<li>{{$det->first_name}} {{$det->last_name}}</li>
						@endforeach
					</ul>

					<p>I have also re-attached the Mobility Suitability Assessment Report Templates which will need to be completed for each candidate and returned to INS at the completion of this process.</p>

					<p>Please click <a href="{{Config::get('app.url')}}site/schedule_interview/{{$details[0]->jobid}}">here</a> to schedule your preferred dates and times for interview.</p>

					<p>Also, please see attached an interview guide. INS were asked to provide these to support Hiring Managers in making the mobility assessment process as efficient as possible. Though, it is up to the Hiring Manager whether they take advantage of the guide.</p>

					<p>Many thanks,<br>INS Mobility Team<br>02 9119 6000</p>                 
                </td>
            </tr>
            
        </table>
    </td>
</tr>


@endsection