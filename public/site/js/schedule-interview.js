$(document).ready(function(){
  var firstdate = '';
  var uris = document.location.href.split('/');
  var job_id = uris[5];
  var date_timings = [];
  $.ajax({
    type: "GET",
    url: "/site/selected_dates/"+job_id,
    dataType: "json",
    success: function(msg){
      console.log(msg)
      if(msg.success == true){
        $.each(msg.assigned_dates, function( index, value ){
          date_timings.push({'seldate':value.interview_dates,'seltime':value.interview_timings});
          $("td.cur-month").each(function(){
            var date = new Date(value.interview_dates);
            if($(this).text() == date.getDate()){
              $(this).css('background','green');
            }
          });
          if(index == 0){
            firstdate = value.interview_dates;
            var date =  new Date(value.interview_dates);
          }
          $('h4.timeIn').each(function(){
            if(($(this).text().trim() == value.interview_timings)&&(value.interview_dates == firstdate)){
              $(this).css('background','green');
              $(this).addClass('seltime');
            }
          });
        });
      } else {
        $(".failure_interview").addClass('alert alert-danger').html(msg.message);
      }
    },
  });//ajax
 
  var cyear = '';
  var cmonth = '';
  var cday = '';
  var unav_dates =  ['2016-01-*','2016-02-*','2016-03-*','2016-04-*','2016-05-*','2016-06-*','2016-07-*','2016-08-*','2016-09-*', '2016-10-*', '2016-11-*', '2016-12-*'];
  var d = new Date();
  var month = d.getMonth()+1;
  var day = d.getDate()-1;
    
  for(i=1 ; i<= day; i++){
    var output = d.getFullYear() + '-' +
        (month<10 ? '0' : '') + month + '-' +
        (i<10 ? '0' : '') + i;
    unav_dates.push(output);
  }
 
  var main_cal = $('#glob-data').calendar({
    unavailable: unav_dates,
    onSelectDate: function(day, month, year){
      cyear = year;
      cmonth = month;
      cday = day;
    }
  });
 
  $(document).on("click", ".available", function(){
    var seldate = cyear +"-"+cmonth+"-"+cday;
    $(this).css('background','green');
    $(this).addClass('seltime');
    $("h4.timeIn").css('background','');
    $("h4.timeIn").removeClass('seltime');
    $.each(date_timings, function( index, value ){
      console.log(value.seldate+"-----"+seldate+"--------"+value.seltime)
      if(value.seldate === seldate){
        $("h4.timeIn:contains("+value.seltime+")").css('background','green');
        $("h4.timeIn:contains("+value.seltime+")").addClass('seltime');
      }
    });
  }); 

  $(document).on("click", "h4.timeIn", function(){
    var selref = $(this);
    if($('.available').hasClass('seltime')){
      var seldate = $('.seldate').text().trim();
      var seltime = $(this).text().trim();
      if(!$(this).hasClass('seltime')){
        var  seldate = cyear +"-"+cmonth+"-"+cday;
        var flag=1;
        $.each(date_timings, function( index, value ){
          if(index < date_timings.length && date_timings[index].seldate == seldate && date_timings[index].seltime === seltime){
            flag =0 ;
            return false;
          }
        });
        if(flag == 1){
          date_timings.push({'seldate':seldate,'seltime':seltime});
          $(this).css('background','green');
          $(this).addClass('seltime');
        }
      } else {
        var seldate = cyear +"-"+cmonth+"-"+cday;
        $(this).removeClass('seltime');
        $.each(date_timings, function( index, value ){
          if(index < date_timings.length && date_timings[index].seldate == seldate && date_timings[index].seltime === seltime){
            selref.css('background','');
            $(this).removeClass('seltime');
            date_timings.splice(index,1);
          }
        });
      }
    } else {
      alert("Please select date!");
    }
  });

  $('.available').click(function(e){
    $('.available').removeClass('seldate');
    $('.available').css('background','');
    $(this).addClass('seldate');
    $(this).css('background','green');
    $.each(date_timings, function( index, value ){
      sdate = date_timings[index].seldate;
      var newdate = new Date(sdate)
      curdate = newdate.getDate();
      $("td.cur-month").each(function(){
        if($(this).text() == curdate){
          $(this).css('background','green');
        }
      });
    });
  });

  $('.confirmInterview').click(function(e){
    var txt ="SCHEDULED INTERVIEW";
    e.preventDefault();
    $('.confirmInterview').html('Processing...');
    $('.confirmInterview').prop("disabled",true);
   
    candidates = [];
    ins_ids = [];
    candidates_screened = [];
    ins_ids_screened = [];
    ins_comments_screened = [];
    var time = $(".time").val();
    var cand = $(".candi");
    var job_id = $(".jobid").val();
    var comments = $("#comments").val();
    var comments_panelmember = $("#comments_panelmember").val();
    var token = $("#token").val();
     
    $(".candi").each(function(index,candidate){
      if($(candidate).is(':checked')){
        if(candidate.value == 'no' ){
           ins_val = $(this).attr('ins-id-no');
           candi_val  = $(this).attr('ins-canid-no');
           ins_comments_scr = $('#screened_description_'+ins_val).val();
           ins_comments_screened.push({
            candiddateid: candi_val, 
            insids_screened: ins_val, 
            comment:  ins_comments_scr
           });
        } else {
          candidates.push(candidate.value);
          ins_ids.push($(this).attr('ins-id-yes'));
        }
      }
    });
 
    if(time == -1){
      $('.confirmInterview').html(txt);
      $('.confirmInterview').prop("disabled",false);
      return false;
    } else {
      var arr = [];
      arr['date_timings']=date_timings;
      arr['time_interval'] = time;
      arr['candidates'] = candidates;
      arr['job_id'] = job_id;
      arr['token'] = token;
      arr['candidates_screened'] = candidates_screened;
      arr['ins_ids_screened'] = ins_ids_screened;
      arr['ins_comments_screened'] = ins_comments_screened;
      var formdata = "date_timings=" + JSON.stringify(date_timings) + "&time_interval=" + time +  "&candidates=" + JSON.stringify(candidates) +  "&job_id=" + job_id +  "&comments=" + comments +  "&comments_panelmember=" + comments_panelmember +  "&ins_ids=" + JSON.stringify(ins_ids)+  "&candidates_screened=" + JSON.stringify(candidates_screened) +  "&ins_ids_screened=" + JSON.stringify(ins_ids_screened) +  "&ins_screened=" + JSON.stringify(ins_comments_screened);
      $.ajax({
        type: "GET",
        url: "/site/post_schedule_interview",
        dataType: "json",
        data: formdata,
        success: function(msg){
          if(msg.success == true){
            window.location = "/site/scheduled_success" ;
          } else {
            $('.confirmInterview').html(txt);
            $('.confirmInterview').prop("disabled",false);
            $(".failure_interview").addClass('alert alert-danger').html(msg.message);
          }
        },
      });//ajax
    }//else
  });

  $('#sel1').on('change', function(){
    date_timings = [];
    $( "td.seldate" ).each(function(index){
      $(this).css('background','green');
      $(this).removeClass('seldate');
    });
    var timeInterval = this.value; 
    if(timeInterval != -1){
      var myDate = new Date("2000-01-01 08:00");
      var intDate = new Date("2000-01-01 08:00");
      var timefrm = ("0"+myDate.getHours()).slice(-2) + ":" + ("0"+myDate.getMinutes()).slice(-2);
      myDate.setTime(myDate.getTime() + (timeInterval * 60 * 1000));
      var myDatefrm = myDate;
      var timeto = ("0"+myDate.getHours()).slice(-2) + ":" + ("0"+myDate.getMinutes()).slice(-2);
      var timeSlot =[];
      var betDate = new Date("2000-01-01 08:00");
      var count=0,mul=0;
      mainloop:
      for(var i=1 ; i<=22 ; i++){
        if(timeSlot.indexOf(timefrm+'-'+timeto) == -1){
          timeSlot.push(timefrm+'-'+timeto);
        }
        timefrm = ("0"+myDate.getHours()).slice(-2) + ":" + ("0"+myDate.getMinutes()).slice(-2);
        myDate.setTime(myDate.getTime() + (timeInterval * 60 * 1000));
        timeto = ("0"+myDate.getHours()).slice(-2) + ":" + ("0"+myDate.getMinutes()).slice(-2);
        count = (timeInterval/30);
        betDate.setTime(betDate.getTime() + (30 * 60 * 1000));
        intDate.setTime(betDate.getTime());
        while((count-1) >= 1){
          bettimefrm = ("0"+intDate.getHours()).slice(-2) + ":" + ("0"+intDate.getMinutes()).slice(-2);
          intDate.setTime(intDate.getTime() + (timeInterval * 60 * 1000));
          bettimeto = ("0"+intDate.getHours()).slice(-2) + ":" + ("0"+intDate.getMinutes()).slice(-2);
          if(timeSlot.indexOf(bettimefrm+'-'+bettimeto) == -1){
            timeSlot.push(bettimefrm+'-'+bettimeto);
          }
          count--;
        }//while
      }//for
      timeSlot.sort(compare);
      timestr = "";
      var j=0;k=0;
      if(timeInterval == 60){
        j=6; k=27;
      } else if(timeInterval == 90) {
        j=5; k=26;
      } else if(timeInterval == 120) {
        j=4; k=25;
      } else {
        j=0; k=21;
      }
      for(; j<=k; j++) {
        timestr += '<div class="col-xs-6 col-sm-4 col-md-3   paddingRightside timeslot"  id="time-slot" data-time="'+timeSlot[j]+'">'
        +'<h4 class="timeIn hoover">'+timeSlot[j]+'<span class="meridian"> </span></h4></div>';
      }
      $('.timeslotdiv').empty();
      $('.timeslotdiv').html(timestr);
    } else {
      alert("Not a valid time interval.");
    }//else
  });

  function compare(a,b) {
    var time1 = parseFloat(a.replace(':','.').replace(/[^\d.-]/g, ''));
    var time2 = parseFloat(b.replace(':','.').replace(/[^\d.-]/g, ''));
    if(a.match(/-*/)) time1 += 12; if(b.match(/-*/)) time2 += 12;
    if (time1 < time2) return -1;
    if (time1 > time2) return 1;
    return 0;
  } 

  $(document).on('click', '.screened-candidates', function(event) {
    id =$(this).attr('ins-id-no');
    $('.skill-screened-outer_'+id).fadeIn();
    $('#screened_description_'+id).val('');
  });

  $(document).on('click', '.close-screened', function(event) {
    $('#screened_description_'+id).removeClass('text_error');
    id = $(this).attr('ins-id-close');
    $('.skill-screened-outer_'+id).fadeOut();
    $('input:radio[ins-id-yes='+id+']').prop('checked', true);
  });

  $(document).on('click', '.submit-screened', function(event) {
    event.preventDefault();
    $('#screened_description_'+id).css('border','1px solid #E1E1E1');
    id = $(this).attr('ins-id-submit');
    text_val = $('#screened_description_'+id).val();
    if($.trim(text_val) == ''){
      $('#screened_description_'+id).css('border','1px solid #ff0000');
      return false;
    }
    $('div[schedule_div_id='+id+']').hide();
    $('.skill-screened-outer_'+id).fadeOut();
  });
});