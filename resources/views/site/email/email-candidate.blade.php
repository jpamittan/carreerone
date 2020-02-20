
@extends('site.email.master-eit')

@section('content')

<tr>
    <td class="one-column">
        <table width="100%">
            <tr>
                <td class="inner contents" style="background-color:#ffffff; padding:20px; text-align: center;">
               		<p><img src="{{Config::get('app.url')}}/site/img/thumbs-up.jpg" /></p>
					<h2 style='color: #BE0071; font-weight: 800;'>How did you go?</h2>
					<h4 style='color: #BE0071; font-weight: 400; text-decoration: underline;'>{{ $details['job_title'] }}</h4>
						<p style="margin-top: -17px;">
							{{ $details['agency_name'] }}<br/>
							{{ $details['interview_date'] }}<br/>
							{{ $details['interview_time'] }}<br/>
							{{ $details['location'] }}
						</p>
						<a style='cursor: pointer;' href="{{ URL::to('/site/feedback/'.$details['crm_user_id'].'/'.$details['interviews_id']) }}" target="_blank"><button style='background: rgb(190, 0, 113) none repeat scroll 0% 0%; color: rgb(255, 255, 255); font-size: 18px; border: medium none; border-radius: 5px; padding: 10px 25px; margin-bottom: 20px; margin-top: 10px;'>
							Tell us how you went
						</button></a>

                     
                </td>
            </tr>
            
        </table>
    </td>
</tr>
@endsection



 