
@extends('site.email.master-eit')

@section('content')

<tr>
    <td class="one-column">
        <table width="100%">
            <tr>
                <td class="inner contents" style="background-color:#ffffff; padding:20px; text-align: center;">
               		<p><img src="{{Config::get('app.url')}}/site/img/thumbs-up.jpg" /></p>
					<h2 style='color: #BE0071; font-weight: 800;'>Interview Reminder</h2>
					<h4 style='color: #BE0071; font-weight: 400; text-decoration: underline;'>{{ $details['job_title'] }}</h4>
						<p style="margin-top: -17px;">
							{{ $details['agency_name'] }}<br/>
							{{ $details['interview_date'] }}<br/>
							{{ $details['interview_time'] }}<br/>
							{{ $details['location'] }}
						</p>
						 

                     
                </td>
            </tr>
            
        </table>
    </td>
</tr>
@endsection



 