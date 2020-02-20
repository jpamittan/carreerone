
$(document).ready(function ()
{
    var unavailable = {};
    var mixed_dates = [];
    var pen_dates = [];

    // Get today's date to highlight in calendar
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1; //January is 0!
    var yyyy = today.getFullYear();
    var ddTemp = dd;
    if(dd<10) { dd='0'+dd } 
    if(mm<10) { mm='0'+mm } 
    today = yyyy+'-'+mm+'-'+dd;
    mixed_dates.push(today);

    $.ajax({
        type: "GET",
        url: "/site/calandar_pending_dates",
        success: function (msg) {
            if (msg.success == true) {
                var d = msg.interview_pending_dates
                var arr = [];
                $(d).each(function (index, dates) {
                    var all_date = dates.interview_dates;
                    mixed_dates.push(all_date);
                    var day = new Date(all_date);
                    unavailable[day.getDate()] = dates.id;
                    pen_dates.push(day.getDate());
                    // console.log(pen_dates);
                });
                // console.log(mixed_dates);
            } else {
                $('#glob-data').calendar({
                    unavailable: []
                });
            }

            $.ajax({
                type: "GET",
                url: "/site/calandar_dates",
                success: function (msg) {
                    if (msg.success == true) {
                        var d = msg.interview_dates
                        var arr1 = [];
                        $(d).each(function (index, dates) {
                            var all_date1 = dates.interview_date;
                            mixed_dates.push(all_date1);
                        });
                        // console.log(mixed_dates);
                        $('#glob-data').calendar({
                            unavailable: mixed_dates
                        });

                        // Change the background and color of today's date cell
                        $("td.unavailable.cur-month:contains('"+ddTemp+"')").css('background','#F7DC6F');
                        $("td.unavailable.cur-month:contains('"+ddTemp+"')").css('color','#aaaaaa');

                        for (var i = 0; i < pen_dates.length; i++) {
                            // console.log(pen_dates[i]);
                            $("td.unavailable:contains("+pen_dates[i]+")").css('background','#C55762');
                        }
                    } else {
                        $('#glob-data').calendar({
                            unavailable: mixed_dates
                        });
                         $('.datetimepicker').find('.unavailable').addClass('unavailable_int');;
          

                        for (var i = 0; i < mixed_dates.length; i++) {
                            
                           // $("td.unavailable:contains("+mixed_dates[i]+")").css('background','#C55762');
                            

                        }
                    }
                },
                error: function () {
                },
            });
        },
        error: function () {
        },
    });

    $(document).on("click","i.prev,i.next",function(){
        for (var i = 0; i < pen_dates.length; i++) {
            // console.log(pen_dates[i]);
            $("td.unavailable:contains("+pen_dates[i]+")").css('background','#C55762');
        }

        // if the calendar get back to the current month retain gray color
        var calMonth = $(".month-name").html();
        var monthNames = ["January", "February", "March", "April", "May", "June","July", "August", "September", "October", "November", "December"];
        var currMonth = monthNames[mm-1];
        if (calMonth.indexOf(currMonth) >= 0 && calMonth.indexOf(yyyy) >= 0) {
            $("td.unavailable.cur-month:contains('"+ddTemp+"')").css('background','#F7DC6F');
            $("td.unavailable.cur-month:contains('"+ddTemp+"')").css('color','#aaaaaa');
        }
    });
    
    $(document).on("click", ".unavailable", function () { // This is the changed line
        var date_id = $(this).html();
        var id = unavailable[date_id.trim()];
        if(id == undefined){
            window.location = "/site/interview" ;
        }else{
        
          window.location = "/site/interview";
        }
    });

    $("div.hoover").click(function () {
    });
});

