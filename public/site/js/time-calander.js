$(document).ready(function ()
{
    var unavailable = {};
    var mixed_dates = [];
    var pen_dates = [];
    var calandar_pending_dates = {};
    var value = $("#pending_id").val();
    $.ajax({
        type: "GET",
        url: "/site/calandar_pending_dates_candidate/"+ value,
        success: function (msg) {

            calandar_pending_dates = msg;

            var d = msg.pending_dates
            var arr = [];
            $(d).each(function (index, dates) {
                var all_date = dates.interview_dates;
                mixed_dates.push(all_date);
//                    console.log(dates);
                var day = new Date(all_date);
                unavailable[day.getDate()] = dates.id;
                pen_dates.push(day.getDate());
                //console.log(dates.interview_timings);
            });


            console.log(unavailable);
            $('#calanda-interview').calendar({
                unavailable: mixed_dates
            }); 
             $('.datetimepicker').find('.unavailable').addClass('unavailable_int').removeClass('unavailable');;
          
        },
        error: function () {

        },
    });

 $(document).on("click", ".next, .prev", function () { // This is the changed line

      $('.datetimepicker').find('.unavailable').addClass('unavailable_int').removeClass('unavailable');;

});

 $(document).on("click", "#time-slot", function () { // This is the changed line

    $("h4.timeIn").each(function (index) {
        if($(this).attr('id') != '')
        {
            $(this).css('background','#C55762');
        }
    });

    var id = $(this).children('h4').attr('id');
    var id1 =  id.replace("t", "");
    $('#interview_id').val(id1);
    $('h4#'+id).css('background','green');

    $('h4.monthDate').html($('td.sel_date').text()+" "+$('div.month-name').text()+" at "+$('h4#'+id).first().text());

});
    //show anthr_sec
    
    //disable jobs section and enable another section
    $(document).on("click", ".unavailable_int", function () { // This is the changed line
        var date_id = $(this).html();
        $("td").removeClass('sel_date');
        $(this).addClass('sel_date');
        $('.unavailable_int').css('background','#C55762');
        $(this).css('background','#008000');
        date_id = date_id.trim();

        $("h4.timeIn").each(function (index) {
            $(this).css('background', '');
            $(this).attr('id', '');

        });
        //console.log(calandar_pending_dates);
        selTimings(date_id);
    });


    function selTimings(date_id)
    {
        $(calandar_pending_dates.pending_dates).each(function (index, cpd) {
            var d = new Date(cpd.interview_dates);
            if (date_id == d.getDate())
            {
                if (cpd.time != 1) {
                    var times = (cpd.time) / 30;
                    var next = 0;
                    while (times >= 1) {
                        next += 30;
                        var timings = cpd.interview_timings;
                        var timeStr = toTime(fromTime(cpd.interview_timings) + fromTime("0:" + next));

                     // console.log(cpd);

                     $("h4.timeIn").each(function (index) {
                        if (timeStr == $(this).text().replace(/ /g, ''))
                        {
                            $(this).css('background', '#C55762');
                            $(this).attr('id', "t"+cpd.id);
                        }
                        if (timings == $(this).text().replace(/ /g, ''))
                        {
                            $(this).css('background', '#C55762');
                            $(this).attr('id', "t"+cpd.id);
                        }
                    });
                     times--;
                 }
             }
             else
             {
                $("h4.timeIn").each(function (index) {

                    $(this).css('background', '#C55762');
                    $(this).attr('id', "t"+cpd.id);
                });

            }
        }

    });
}

function fromTime(time) {
    var timeArray = time.split(':');
    var hours = parseInt(timeArray[0]);
    var minutes = parseInt(timeArray[1]);

    return (hours * 60) + minutes;
}

function toTime(number) {
    var hours = Math.floor(number / 60);

    var minutes = number % 60;

    return hours + ":" + (minutes <= 9 ? "0" : "") + minutes;
}
$(document).on("click", "#conf-inter", function () {
 var formdata = $( "#acpt-form" ).serialize();
 $.ajax({
    type: "POST",
    data:formdata,
    url: "/site/accept_interview",
    success: function(msg) {
        if(msg.success == true){
            window.location = "/site/interview" ;
        }else{
            $(".failure_interview").addClass('alert alert-danger').html(msg.message);
        }


    },
    error: function () {

    },
});

});
$("#rejectbox" ).hide();
$(document).on("click", "#reject-inter", function () {
    $("#rejectbox" ).show();
    $("#reject-inter" ).hide();
    $("#conf-inter" ).hide();
});
$(document).on("click", "#cancel-back", function () {
    $("#rejectbox" ).hide();
    $("#reject-inter" ).show();
    $("#conf-inter" ).show();
});
$(document).on("click", "#send-mess", function () {
    var formdata = $( "#acpt-form" ).serialize();
    $.ajax({
        type: "POST",
        data:formdata,
        url: "/site/reject_interview",
        success: function(msg) {
            if(msg.success == true){
                window.location = "/site/interview" ;
            }


        },
        error: function () {

        },
    });
});

}); 

