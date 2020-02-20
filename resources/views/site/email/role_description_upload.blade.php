@extends('site.email.master')
@section('content')
<tr>
    <td class="one-column">
        <table width="100%">
            <tr>
                <td class="inner contents" style="background-color:#ffffff; padding:20px;">
					<p>
						Please see attached the RD for <b>{{$details->job_title}}</b> (req no. <b>{{$details->vacancy_reference_id}}</b>).
					</p>
					<p>The following details have been confirmed:</p>
					<p><b>Workplace location: </b>{{$details->location}}</p>
					<p><b>Hiring Manager contact details:</b><br>
					Name: {{$details->prepared_by_name}}<br>
					Phone: {{$details->prepared_by_number}}<br>
					Email: {{$details->prepared_by_email}}</p>
					@if($details->length_term == '121660000')
					    <p><b>Length of fixed term:</b> 6 months</p>
					@elseif ($details->length_term == '121660001')
					    <p><b>Length of fixed term:</b> 12 months</p>
					@elseif ($details->length_term == '121660002')
					    <p><b>Length of fixed term:</b> 2 years</p>
					@elseif ($details->length_term == '121660003')
					    <p><b>Length of fixed term:</b> 3 years</p>
					@elseif ($details->length_term == '121660004')
					    <p><b>Length of fixed term:</b> 4 years</p>
					@elseif ($details->length_term == '121660005')
					    <p><b>Length of fixed term:</b> 5 years</p>
					@elseif ($details->length_term == '121660006')
						@if(!empty($details->length_term_other))
					    	<p><b>Length of fixed term:</b> {{$details->length_term_other}}</p>
					    @else
					    	<p><b>Length of fixed term:</b> Not provided</p>
					@else
					    <p><b>Length of fixed term:</b> Not provided</p>
					@endif
					<p><b>Advertisement:</b>
					@if(!empty($advert ))
						<br>{!! html_entity_decode( nl2br(e($advert)) )  !!}
					@else
						Not provided
					@endif
                </td>
            </tr>
        </table>
    </td>
</tr>
@endsection