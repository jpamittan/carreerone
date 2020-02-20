
@extends('site.email.master-eit')

@section('content')

<tr>
    <td class="one-column">
        <table width="100%">
            <tr>
                <td class="inner contents" style="background-color:#ffffff; padding:20px; text-align: center;">
               
					<h2 style='color: #BE0071; font-weight: 800;'>Hi {{$datail['name'] }},</h2>
					<p>
						<h5>Your  application has been submitted for  </h5>
						<h6>Role Title:  {{$datail['jobtitle'] }}</h6>
						 
						<h6>Grade:  {{$datail['jobgrade'] }}</h6>
						<h6>Salary:  {{$datail['salarypackage'] }}</h6>
					</p>



					 

					 <p  >
					 		<h4>Candidate Information: </h4>
							<h6>Name : {{$datail['name'] }}</h6>
				            <h6>Email : {{$datail['email'] }}</h6>
				            <h6>Contact  : {{ $datail['contact'] }}</h6>
				            <h6>Other Contact  : {{$datail['othercontact'] }}</h6>
				            <h6>Resume category  : {{$datail['resumecategory'] }}</h6>
  				            <h6>Resume Link : {{$datail['resumelink'] }}</h6>
  				             @if(!empty($datail['ins_covering_letter']))
  				             	<h6>Cover letter  link : {{$datail['ins_covering_letter'] }}</h6>
  				            @endif

  				            @if(!empty($datail['ins_supporting_doc']))
  				             	 <h6>Supporing document link : {{$datail['ins_supporting_doc'] }}</h6>
  				            @endif
  				              
					</p>



                     
                </td>
            </tr>
            
        </table>
    </td>
</tr>


@endsection

 