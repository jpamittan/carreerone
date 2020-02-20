$(".table2").hide();
$(".table3").hide();
// var dots = "......";
$(document).ready(function() {
    $(".navbar-toggle").click(function(){
                        $(".header_row").addClass("open");
                });
                $(".top").click(function(){
                        $(".header_row").removeClass("open");
                });
    // $(".description").text(function(index, currentText) {
    //     return currentText.substr(0, 200) + dots;
    // });
    $('#jobMatch').DataTable({
        "lengthChange": false,
        "lengthMenu": [20, 40, 60, 80, 100],
        "ordering": false,
        "pageLength": 20,
        "info": false
    });
    $('#ahis').DataTable({
        "lengthChange": false,
        "lengthMenu": [20, 40, 60, 80, 100],
        "ordering": false,
        "pageLength": 12,
        "info": false

    });
    $('#jobMatch1').DataTable({
        "lengthChange": false,
        "lengthMenu": [20, 40, 60, 80, 100],
        "ordering": false,
        "pageLength": 20,
        "info": false
    });
    $('#jobMatch2').DataTable({
        "lengthChange": false,
        "lengthMenu": [20, 40, 60, 80, 100],
        "ordering": false,
        "pageLength": 20,
        "info": false
    });
   

    $("#one").click(function(){
        $(".table1").hide();
        $(".table2").show();
        $(".table3").hide();
    });
    $("#two").click(function(){
        $(".table1").hide();
        $(".table2").hide();
        $(".table3").show();
    });
    $("#three").click(function(){
        $(".table1").show();
        $(".table2").hide();
        $(".table3").hide();
    });

   
    
});
       