@extends('site.email.master')

@section('content')

    <tr>
        <td class="one-column">
            <h3>Failed to parse RD</h3>
            <p>Error: <em>{{!empty($errorMessage) ? $errorMessage  : "No error returned"}}</em></p>

            <?php if(!empty($rdSenderDetails)): ?>
            <?php if($rdSenderDetails instanceof \App\Models\Entities\Jobs): ?>
            <?php $agency = !empty($rdSenderDetails->agency_branch_id) ? \App\Models\Entities\AgencyBranch::find($rdSenderDetails->agency_branch_id) : null; ?>
            <table style="width: 100%">
                <tr>
                    <td>Vacancy Reference:</td>
                    <td style="text-align: right">{{$rdSenderDetails->vacancy_reference_id}}</td>
                </tr>
                <tr>
                    <td>Role Title:</td>
                    <td style="text-align: right">{{$rdSenderDetails->job_title}}</td>
                </tr>
                <tr>
                    <td>Agency:</td>
                    <td style="text-align: right">{{!empty($agency) ? $agency->location_name  : ""}}</td>
                </tr>
                <tr>
                    <td>Error date:</td>
                    <td style="text-align: right">{{\Carbon\Carbon::now()->format('d/m/Y H:i a')}}</td>
                </tr>
            </table>
            <?php elseif($rdSenderDetails instanceof \App\Models\Entities\Employee): ?>
            <?php $caseManager = !empty($rdSenderDetails->ownerid) ? \App\Models\Entities\CaseManager::where(['systemuserid' => $rdSenderDetails->ownerid])->first() : null; ?>
            <table style="width: 100%">
                <tr>
                    <td>EIT name:</td>
                    <td style="text-align: right">{{$rdSenderDetails->new_firstname." ".$rdSenderDetails->new_surname}}</td>
                </tr>
                <tr>
                    <td>CPO Name:</td>
                    <td style="text-align: right">{{!empty($caseManager) ? $caseManager->fullname: ""}}</td>
                </tr>
                <tr>
                    <td>Error date:</td>
                    <td style="text-align: right">{{\Carbon\Carbon::now()->format('d/m/Y H:i a')}}</td>
                </tr>
            </table>
            <?php endif ?>
            <?php endif ?>

            <?=!empty($detailedError) ? "<h4>Data received after parsing the RD:</h4>" : "" ?>
            {!! !empty($detailedError) ? $detailedError : "" !!}
            <p>Please find the RD attached to this email.</p>
        </td>
    </tr>
@endsection



