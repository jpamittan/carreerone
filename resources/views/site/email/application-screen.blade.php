
@extends('site.email.master')

@section('content')

<tr>
    <td class="one-column">
        <table width="100%">
            <tr>
                <td class="inner contents" style="background-color:#ffffff; padding:20px;">
					<p>Please see below some brief feedback for candidates who have been screened on application for {{$job->job_title}} (req no. {{$job->vacancy_reference_id}}).</p>

					<ul>
						@foreach ($eits as $eit)
						<li>{{$eit->first_name}} {{$eit->last_name}}
						<p><b>{{$eit->comment}}</b></p>
						</li>
						@endforeach
					</ul>

					<p>Please advise the unsuccessful candidates of the outcome ASAP. Any successful candidates will be advised at 5pm today.</p>

					<p>More detailed feedback will be provided by the Hiring Manager for all candidates at the completion of this process.</p>
                </td>
            </tr>
            
        </table>
    </td>
</tr>


@endsection