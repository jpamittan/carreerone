



@extends('site.email.master')

@section('content')

<tr>
    <td class="one-column">
        <table width="100%">
            <tr>
                <td class="inner contents" style="background-color:#ffffff; padding:20px; text-align: center;">
               
					<h2 style='color: #BE0071; font-weight: 800;'>Hi,</h2>
				 
					<h4  >Profile completed for {{$user->first_name}} {{$user->last_name}} </h2>
					 
					<p style="margin-top: -17px;">
						<h6>Name: {{$user->first_name}} {{$user->last_name}}</h6>
						<h6>ID: {{$user->crm_user_id}}</h6>
						<h6>Email: {{$user->email}}</h6>
					 

						 
						</p>
 

						 



                     
                </td>
            </tr>
            
        </table>
    </td>
</tr>


@endsection

