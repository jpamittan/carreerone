@extends('site.layouts.master');

@section('content')
    <section>
        <div class="container-fluid second_section">
            <div class="container container-whitebg">
                <div class="row">
                    <div class="col-sm-12">
                        @include('common.errors.jumbotron', ['title' => 'Page not found', 'body' => '<p>The page you requested could not be found.
        Use your browser\'s <b>Back</b> button to navigate to the page you have previously come
        from or click the below button to return to the Dashboard page.</p>
    <a href=".URL::to('/')." class="btn btn-primary btn-large">
        <i class="icon-home icon-white"></i> Take Me Home</a>'])
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection