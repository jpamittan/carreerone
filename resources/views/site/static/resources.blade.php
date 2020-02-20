@extends('site.layouts.master-dashboard')

@section('content')
    <div class="container container-whitebg1">
        <div class="row" style="padding: 20px">
            {!! !empty($body) ? $body : "" !!}
        </div>
    </div>
@endsection