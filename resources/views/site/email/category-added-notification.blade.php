
@extends('site.email.master')

@section('content')

<tr>
    <td class="one-column">
        <table width="100%">
            <tr>
                <td class="inner contents" style="background-color:#ffffff; padding:20px; text-align: center;">
               
					<h2 style='color: #BE0071; font-weight: 800;'>Hi 
                    @if(!empty( $datail['cname']   )) {{ $datail['cname'] }} @endif  </h2>
					<p>
						A Category is added to the profile of  <b>{{$datail['name']}}</b>
					</p>

<p  >
			<h5>Name : {{$datail['name'] }}</h5>
            <h5>Email : {{$datail['email'] }}</h5>
           <h5>Contact  : {{ $datail['contact'] }}</h5>
            <h5>Other Contact  : {{$datail['othercontact'] }}</h5>
            <h5>Category: {{$datail['category'] }}</h5>
             <h5>Message : {{ $datail['msg'] }}</h5>

          	
					
						 
							 
							 
					</p>

                     
                </td>
            </tr>
            
        </table>
    </td>
</tr>


@endsection