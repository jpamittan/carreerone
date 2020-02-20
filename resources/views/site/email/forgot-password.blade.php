
@extends('site.email.master-eit')

@section('content')

<tr>
    <td class="one-column">
        <table width="100%">
            <tr>
                <td class="inner contents" style="background-color:#ffffff; padding:20px;">
                	 
					<h2 style='color: #BE0071; font-weight: 800;'>Hi {{ $details['name'] }},</h2>
					<p>
						This email will expire at {{ $details['expire_in'] }}
					</p>

					 <p>Please click the link below to reset your password</p>
					<a href="{{ URL::to('site/reset-password/'.$details['token']) }}" target="_blank">Click here to reset password</a>

					<br>
							<BR><BR>or copy url <br>{{ URL::to('site/reset-password/'.$details['token']) }}
                     
                </td>
            </tr>
            
        </table>
    </td>
</tr>


@endsection



 