
@extends('site.email.master')

@section('content')

<tr>
    <td class="one-column">
        <table width="100%">
            <tr>
                <td class="inner contents" style="background-color:#ffffff; padding:20px; text-align: center;">
               
					<h2 style='color: #BE0071; font-weight: 800;'>Hi Vacancy Team,</h2>
					<p>
						The following applications have been received for the role of <b>{{$details[0]->job_title}}</b>
					</p>

					<h4 style='color: #BE0071; font-weight: 400; text-decoration: underline;'>{{$details[0]->job_title}}</h4>
					<p  >
						<h5>List Of Job Applicants</h5>
							@foreach($details as $det)
							<h5>{{$det->first_name}} {{$det->last_name}}</h5>
							@endforeach
							<h5>The Hiring Manager for this role can schedule interviews at the following link </h5>
							<h4><a href="{{Config::get('app.url')}}site/schedule_interview/{{$details[0]->jobid}}">Click here to schedule interview</a>

							</h4>
							<br>
							or copy url <br>{{Config::get('app.url')}}site/schedule_interview/<br>{{$details[0]->jobid}}
					</p>

                     
                </td>
            </tr>
            
        </table>
    </td>
</tr>


@endsection