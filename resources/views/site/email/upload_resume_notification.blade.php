
@extends('site.email.master')

@section('content')

<tr>
    <td class="one-column">
        <table width="100%">
            <tr>
                <td class="inner contents" style="background-color:#ffffff; padding:20px; text-align: left;">

					<p>{{$datail['name']}} has uploaded an updated {{$datail['resumecategory'] }} resume.
					</p>
                    <p>Please review ASAP and provide feedback if necessary.</p>
                    <p>A member of the Mobility Team will save this resume against {{$datail['first_name']}}'s profile in CRM ASAP.</p>
                    <p>Please note, if this candidate is matched to a role and does not upload a tailored resume by 3pm on the application due date, the resume they have uploaded with the same occupational category of the role will be sent. If neither of these have been uploaded, their master resume will be sent.</p>
							 

                </td>
            </tr>
            
        </table>
    </td>
</tr>


@endsection