
@extends('site.email.master')

@section('content')

<tr>
    <td class="one-column">
        <table width="100%">
            <tr>
                <td class="inner contents" style="background-color:#ffffff; padding:20px; text-align: center;">
               		

					<h2 style='color: #BE0071; font-weight: 800;'>Candidate feedback?</h2>
					
						<p>Candidate Name: {{ $details['fullname'] }}</p>
                        <p>Candidate Email: {{ $details['email'] }}</p>
                        <p>Feedback:<p>
                        {{ $details['feedback'] }}

                     
                </td>
            </tr>
            
        </table>
    </td>
</tr>
@endsection



  