@extends('site.layouts.master-iframe')

@section('content')
    <section class="inter-info">
        <div class="container-fluid container-whitebg ">
            <div class="container second_section">
                <div class="col-sm-12 col-md-12 jobCapabulites" style="background-color: white;">
                    <div class="col-sm-12 col-md-12">
                        @include('site.partials.thirdlevel-match',array('capabilities'=>$capabilities,'score'=>$score))
                        @include('site.partials.secondlevel-match',array('skills'=>$skills))
                    </div>
                </div>
            </div>
        </div>
    </section>
    <script type="text/javascript">
        window.c1resize = function () {
            window.parent.postMessage(
                // get height of the content
                {
                    'module': 'iframe2',
                    'height': Math.max($('.inter-info div').outerHeight() + 10, $('.ins').outerHeight() + 60)
                }
                // set target domain
                , "*"
            )
        };
        $(function () {
            window.c1resize();
        })
    </script>
@endsection