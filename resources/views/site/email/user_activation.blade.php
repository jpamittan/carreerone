
@extends('site.email.master-eit')

@section('content')

<tr>
    <td class="one-column">
        <table width="100%">
            <tr>
                <td class="inner contents" style="background-color:#ffffff; padding:20px; text-align: center;">
               
					 
					<p>
						Verify Your Email Address
					</p>

					 
					<p  >
						  Thanks for creating an account. <BR>
				          Please follow the link below to verify your email address

				           <a href="{{ URL::route('user-activate' , $user->password) }}"> Click here </a>

				           <BR><BR>or copy link <BR>
				           {{ URL::route('user-activate' , $user->password) }}<br/>
					</p>

					<p>To visit the site at anytime, go to <a href="{{ URL::route('site-login-home') }}">{{ URL::route('site-login-home') }}</a>.</p>

                     
                </td>
            </tr>
            
        </table>
    </td>
</tr>


@endsection