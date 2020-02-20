
@extends('site.email.master-eit')

@section('content')

<tr>
    <td class="one-column">
        <table width="100%">
            <tr>
                <td class="inner contents" style="background-color:#ffffff; padding:20px; text-align: center;">
               
					 
					<p>
						Email Address change notification
					</p>

					 
					<p  >
						  Your email address has been updated.  
						  <h6>Old email : {{$oldemail}}</h6>
						  <h6>New email : {{$newemail}}</h6>
				     </p>

					 
                     
                </td>
            </tr>
            
        </table>
    </td>
</tr>


@endsection