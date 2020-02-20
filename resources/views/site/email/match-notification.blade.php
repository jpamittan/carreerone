
@extends('site.email.master')

@section('content')

<tr>
    <td class="one-column">
        <table width="100%">
            <tr>
                <td class="inner contents" style="background-color:#ffffff; padding:20px;">
                	<p>Hi {{$job->prepared_by_name}},</p>

					<p>We have reviewed the information provided for {{$job->job_title}} (req no. {{$job->vacancy_reference_id}}) with {{$agency->agency_name}}. Please accept our request for a mobility assessment for the following NDIS Mobility Pathway Candidate who has been deemed suitable match to the role:</p>

					<ul>
						@foreach ($eits as $eit)
						<li>{{$eit->new_firstname}} {{$eit->new_surname}}</li>
						@endforeach
					</ul>

					<p>Please see attached a Mobility Suitability Assessment Report Template which includes the candidate's referral details. This report will need to be completed and forwarded to INS at the conclusion of the mobility assessment process. We have also attached information for you and the Hiring Manager on the Mobility Pathway.</p>

					<p>All mobility assessments must comprise of: </p>
					
					<ol>
						<li>A resume - provided by INS</li>
						<li>A capability assessment report - provided by INS</li>
						<li>An interview - completed by Agency</li>
						<li>Two referee checks - completed by Agency</li>
					</ol>

					<p>I will provide the candidates resume and capability assessment reports to you by COB tomorrow.</p>

					<p>In the meantime, if you have any questions please don’t hesitate to contact me on the details below.</p>

					<p><b>Please note, advertising for this role cannot proceed until the mobility assessment process is completed.</b></p>

					<p>Many thanks,<br>INS Mobility Team</p>
                </td>
            </tr>
            
        </table>
    </td>
</tr>


@endsection