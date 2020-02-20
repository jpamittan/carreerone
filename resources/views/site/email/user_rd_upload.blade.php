
@extends('site.email.master')

@section('content')

<tr>
    <td class="one-column">
        <table width="100%">
            <tr>
                <td class="inner contents" style="background-color:#ffffff; padding:20px; text-align: center;">
                	<?php /* <p><img src="{{Config::get('app.url')}}/site/img/thumbs-up.jpg" /></p> */?>
					<h2 style='color: #BE0071; font-weight: 800;'>Hi Vacancy Team,</h2>
					<p>
						New user created, please upload the  user  capabilities for - 
					</p>

						<h4 style='color: #BE0071; font-weight: 400; text-decoration: underline;'>{{$user->first_name}}</h4>
						 
						<h5>Please click on the following link to Upload user capabilities</h5>

						<h4><a href="{{Config::get('app.url')}}site/user_capabilities/{{$user->crm_user_id}}">Click here to upload user capabilities</a> </h4>
							<br> or copy url <br>{{Config::get('app.url')}}site/user_capabilities/<br>{{$user->crm_user_id}}


                    
                </td>
            </tr>
            
        </table>
    </td>
</tr>


@endsection

 