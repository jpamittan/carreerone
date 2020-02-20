<section class="applySection">
            <div class="row">
               <div class="col-xs-12 col-sm-12 col-md-12 second_job jobmatchYout paddingNone">
                    <div class="col-xs-12 col-sm-12 col-md-12 sort">
                        <h3 class="">Apply History</h3>
                    </div>
                    <div class="col-sm-12 col-md-12 apthead">
                            <div class="col-md-3 col-sm-3 paddingRight">Role Title/Company</div>
                            <div class="col-md-2  col-sm-2 paddingNone">&nbsp;</div>
                            <div class="col-md-2 col-sm-2 paddingNone sta-marg">Status</div>
                            <div class="col-md-1 col-sm-1 paddingNone applie-marg">Date</div>
                            <div class="col-md-2 col-sm-2 paddingNone">&nbsp;</div>
                           <!--  <div class="col-md-1 col-sm-1 paddingNone">Delete</div>-->
                        </div>
                        <div class="applyHistory-content">
                            @include('site.partials.applyhistorypage', array('apply_history' => $apply_history,'job_latest' =>$job_latest))
                        </div>
                        
                </div>
            </div>
        </section>

    <script>
$(document).ready(function() {
    $( ".delete" ).click(function(e) {
        c = confirm('You Want to delete the job From History');
         e.preventDefault();
         if( c ){
              var formdata = $( "#deletejob" ).serialize();
                  $.ajax({
                    type: "POST",
                    data:formdata,
                    url: "{{URL::route('site.job.delete')}}",
                    success: function(msg){
                        window.location.reload();
                    },
                    error: function() {
                    },
                });
              }
     });
    });

   // Job Apply History pagination
    $(document).on('click', '.applyhistoryPagination a', function(e){
        e.preventDefault();
        var page = $(this).attr('href').split('page=')[1];

        $.ajax({
            url: '/site/dashboard/applyhistorypage?page=' + page
        }).done(function(data){
            $('.applyHistory-content').html(data);
        });
    });
</script>

