
@extends('site.email.master-eit')

@section('content')

<tr>
    <td class="one-column">
        <table width="100%">
            <tr>
                <td class="inner contents" style="background-color:#ffffff; padding:20px; text-align: center;">
               
					 
					<p><strong>You have successfully change your password.</strong></p>
					 
					 
					<p  >
						 
						 
							<p>Please click the link below to login to your account</p>
							@if($details['role_id']==2 || $details['role_id']==1)
								<a href="{{ URL::to('admin/login') }}" target="_blank">{{ URL::to('admin/login') }}</a>
							@else
								<a href="{{ URL::to('site/login') }}" target="_blank">{{ URL::to('site/login') }}</a>
							@endif
					</p>

                     
                </td>
            </tr>
            
        </table>
    </td>
</tr>


@endsection


 