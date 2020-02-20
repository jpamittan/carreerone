@extends('site.layouts.master')

@section('content')
    <!-- success -->
    @if(!empty(\Session::get('message')))
        <div class="alert alert-success">
            {{\Session::get('message')}}
        </div>
    @endif
    <div class="container">
<div class="row">
            <iframe id="c1_iframe1" src="{{URL::to('/site/candidate_detail_info/'.$user_id.'/'.$job_id)}}"
                    class="iframe_aboutus" frameBorder="0" style='width: 100%; height: 100%;'></iframe>
            <iframe id="c1_iframe2" src="{{URL::to('/site/candidate_detail_matching/'.$user_id.'/'.$job_id)}}"
                    class="iframe_aboutus" frameBorder="0" style='width: 100%; height: 100%;'></iframe>
            <iframe id="c1_iframe3" src="{{URL::to('/site/candidate_detail_profile/'.$user_id.'/'.$job_id)}}"
                    class="iframe_aboutus" frameBorder="0" style='width: 100%; height: 100%;'></iframe>
       </div>
    </div>
    <!-- /success -->
    <!--      <div class="col-xs-12 col-sm-12 col-md-12 iframe-info">
           <iframe class="iframe-info1" src="{{URL::to('/site/candidate_detail_info/'.$user_id.'/'.$job_id)}}"></iframe>
      </div>
     <div class="col-xs-12 col-sm-12 col-md-12 iframe-marg iframe-info">
            <iframe class="iframe-match" src="{{URL::to('/site/candidate_detail_matching/'.$user_id.'/'.$job_id)}}"></iframe>
       </div>
      <div class="col-xs-12 col-sm-12 col-md-12 iframe-info">
           <iframe class="iframe-profile"  src="{{URL::to('/site/candidate_detail_profile/'.$user_id.'/'.$job_id)}}"></iframe>
      </div> -->

    <style>


        iframe {
            width: 100%;
        }
    </style>
    <script src="{{URL::to('/site/js/iframe.js')}}"></script>
    <script type="text/javascript">

        $(document).ready(function () {
            /* var iFrameID = document.getElementById('c1_if1');
             var iFrameID2 = document.getElementById('c1_if2');
             var iFrameID3 = document.getElementById('c1_if3');


                   // here you can make the height, I delete it first, then I make it again
                   iFrameID.height = "";
                   iFrameID.height = iFrameID.contentWindow.document.body.scrollHeight + (280*1) +"px";

                   iFrameID2.height = "";
                   iFrameID2.height = iFrameID2.contentWindow.document.body.scrollHeight + (730*1) +"px";

                   iFrameID3.height = "";
                   iFrameID3.height =  (2100*1) +"px";

*/
        });
    </script>
    </body>
    </html>
@endsection
	
						
